type Chapter = { start: number; title: string }

const SEEK_STEP_SECONDS = 5
const SEEK_PAGE_STEP_SECONDS = 30
const PLAYBACK_RATES = [0.75, 1, 1.25, 1.5, 2]
const VALUETEXT_INTERVAL_MS = 5_000
const SHARE_LABEL = 'Link zur aktuellen Stelle teilen'
const STORAGE_PREFIX = 'tw-player:position:'
const STORAGE_INTERVAL_MS = 3_000
const STORAGE_MIN_SECONDS = 5
const STORAGE_END_MARGIN_SECONDS = 10

const pad = (value: number): string => String(value).padStart(2, '0')

const formatClock = (totalSeconds: number): string => {
  const seconds = Math.max(0, Math.floor(totalSeconds))
  const hours = Math.floor(seconds / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  const rest = seconds % 60
  return hours > 0 ? `${hours}:${pad(minutes)}:${pad(rest)}` : `${minutes}:${pad(rest)}`
}

const formatSpoken = (totalSeconds: number): string => {
  const seconds = Math.max(0, Math.floor(totalSeconds))
  const hours = Math.floor(seconds / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  const rest = seconds % 60
  const parts: Array<string> = []
  if (hours > 0) parts.push(`${hours} ${hours === 1 ? 'Stunde' : 'Stunden'}`)
  if (minutes > 0) parts.push(`${minutes} ${minutes === 1 ? 'Minute' : 'Minuten'}`)
  if (rest > 0 || parts.length === 0) parts.push(`${rest} ${rest === 1 ? 'Sekunde' : 'Sekunden'}`)
  return parts.join(' ')
}

const clamp = (value: number, min: number, max: number): number =>
  Math.min(Math.max(value, min), max)

const parseChapters = (raw: string | undefined): Array<Chapter> => {
  try {
    const parsed: unknown = JSON.parse(raw ?? '[]')
    return Array.isArray(parsed) ? (parsed as Array<Chapter>) : []
  } catch {
    return []
  }
}

class TwAudioPlayer extends HTMLElement {
  #audio!: HTMLAudioElement
  #toggle!: HTMLButtonElement
  #seek!: HTMLInputElement
  #current!: HTMLElement
  #total!: HTMLElement
  #status!: HTMLElement
  #error!: HTMLElement
  #chapterButtons: Array<HTMLButtonElement> = []
  #chapters: Array<Chapter> = []
  #duration = 0
  #scrubbing = false
  #lastValuetext = 0
  #lastStored = 0
  #dockObserver: IntersectionObserver | null = null
  #scrolledPast = false
  #started = false
  #skipBack = 15
  #skipForward = 30

  connectedCallback(): void {
    const audio = this.querySelector('audio')
    if (!audio || this.dataset.ready === '1') {
      return
    }

    this.#audio = audio
    this.#audio.controls = false
    this.#duration = Number(this.dataset.duration) || 0
    this.#chapters = parseChapters(this.dataset.chapters)
    this.#skipBack = Number(this.dataset.skipBack) || 15
    this.#skipForward = Number(this.dataset.skipForward) || 30

    this.#render()
    this.#bindAudio()
    this.#setupMediaSession()
    this.#setupDock()
    this.#restorePosition()
    this.dataset.ready = '1'
  }

  disconnectedCallback(): void {
    this.#dockObserver?.disconnect()
  }

  get playtime(): number {
    return this.#audio.currentTime * 1000
  }

  play(): Promise<void> {
    return this.#audio.play()
  }

  pause(): void {
    this.#audio.pause()
  }

  seek(milliseconds: number): void {
    this.#seekTo(milliseconds / 1000)
  }

  #render(): void {
    const poster = this.dataset.poster ?? ''
    const title = this.dataset.title ?? ''
    const show = [this.dataset.show, this.dataset.number].filter(Boolean).join(' ')

    const shareItem = `
      <span class="share-item has-tooltip">
        <button type="button" class="share" data-share-current aria-label="${SHARE_LABEL}">
          <span class="msi-share" aria-hidden="true"></span>
        </button>
        <span class="tooltip" role="tooltip" aria-hidden="true">${SHARE_LABEL}</span>
      </span>
    `

    const ui = document.createElement('div')
    ui.className = 'player-ui'
    ui.innerHTML = `
      <div class="layout">
      ${poster ? '<img class="cover" alt="" width="160" height="160" loading="lazy">' : ''}
      <div class="info">
        <p class="title"></p>
        <p class="show"></p>
      </div>
      <div class="transport">
        <button type="button" class="skip back">
          <span class="msi-replay" aria-hidden="true"></span>
          <span class="skip-value" aria-hidden="true">${this.#skipBack}</span>
        </button>
        <button type="button" class="toggle" aria-label="Abspielen">
          <span class="msi-play-arrow" aria-hidden="true"></span>
        </button>
        <button type="button" class="skip forward">
          <span class="msi-replay" aria-hidden="true"></span>
          <span class="skip-value" aria-hidden="true">${this.#skipForward}</span>
        </button>
        ${shareItem}
      </div>
      <div class="timeline">
        <time class="current">0:00</time>
        <input type="range" class="seek" aria-label="Zeitposition" min="0" step="1" value="0">
        <time class="total">${formatClock(this.#duration)}</time>
      </div>
      <div class="options">
        <label class="rate">
          <span class="sr-only">Wiedergabegeschwindigkeit</span>
          <select>
            ${PLAYBACK_RATES.map(
              (rate) =>
                `<option value="${rate}"${rate === 1 ? ' selected' : ''}>${String(rate).replace('.', ',')}×</option>`
            ).join('')}
          </select>
        </label>
        <button type="button" class="mute" aria-label="Ton aus">
          <span class="msi-volume-up" aria-hidden="true"></span>
        </button>
        <input type="range" class="volume" aria-label="Lautstärke" min="0" max="1" step="0.05" value="1">
        ${shareItem}
      </div>
      <p class="status sr-only" role="status"></p>
      <p class="error" role="alert" hidden></p>
      </div>
    `

    this.append(ui)

    const query = <T extends HTMLElement>(selector: string): T => ui.querySelector<T>(selector)!
    ui.querySelector('.show')!.textContent = show
    ui.querySelector('.title')!.textContent = title
    const cover = ui.querySelector<HTMLImageElement>('.cover')
    if (cover) cover.src = poster

    this.#toggle = query('.toggle')
    this.#seek = query('.seek')
    this.#current = query('.current')
    this.#total = query('.total')
    this.#status = query('.status')
    this.#error = query('.error')

    query<HTMLButtonElement>('.skip.back').setAttribute(
      'aria-label',
      `${this.#skipBack} Sekunden zurück`
    )
    query<HTMLButtonElement>('.skip.forward').setAttribute(
      'aria-label',
      `${this.#skipForward} Sekunden vor`
    )

    this.#updateDuration(this.#duration)
    this.#bindControls(ui)

    if (this.#chapters.length > 0) {
      this.#renderChapters()
    }
  }

  #renderChapters(): void {
    const details = document.createElement('details')
    details.className = 'chapters'
    const summary = document.createElement('summary')
    summary.textContent = 'Kapitel'
    const list = document.createElement('ol')

    for (const chapter of this.#chapters) {
      const item = document.createElement('li')
      const button = document.createElement('button')
      button.type = 'button'
      button.innerHTML = `<time></time><span></span>`
      button.querySelector('time')!.textContent = formatClock(chapter.start)
      button.querySelector('span')!.textContent = chapter.title
      button.addEventListener('click', () => {
        this.#seekTo(chapter.start)
        void this.#audio.play().catch(() => {})
      })
      item.append(button)
      list.append(item)
      this.#chapterButtons.push(button)
    }

    details.append(summary, list)
    this.querySelector('.layout')!.append(details)
  }

  #bindControls(ui: HTMLElement): void {
    this.#toggle.addEventListener('click', () => {
      if (this.#audio.paused) {
        void this.#audio.play().catch(() => this.#showError())
      } else {
        this.#audio.pause()
      }
    })

    ui.querySelector('.skip.back')!.addEventListener('click', () => this.#skip(-this.#skipBack))
    ui.querySelector('.skip.forward')!.addEventListener('click', () =>
      this.#skip(this.#skipForward)
    )

    this.#seek.addEventListener('input', () => {
      this.#scrubbing = true
      this.#renderTime(Number(this.#seek.value))
    })
    this.#seek.addEventListener('change', () => {
      this.#scrubbing = false
      this.#seekTo(Number(this.#seek.value))
    })
    this.#seek.addEventListener('keydown', (event) => {
      const steps: Record<string, number> = {
        ArrowLeft: -SEEK_STEP_SECONDS,
        ArrowDown: -SEEK_STEP_SECONDS,
        ArrowRight: SEEK_STEP_SECONDS,
        ArrowUp: SEEK_STEP_SECONDS,
        PageDown: -SEEK_PAGE_STEP_SECONDS,
        PageUp: SEEK_PAGE_STEP_SECONDS,
      }
      const step = steps[event.key]
      if (step === undefined) return
      event.preventDefault()
      this.#skip(step)
    })

    ui.querySelector<HTMLSelectElement>('.rate select')!.addEventListener('change', (event) => {
      const rate = Number((event.target as HTMLSelectElement).value)
      this.#audio.playbackRate = rate
    })

    const mute = ui.querySelector<HTMLButtonElement>('.mute')!
    const volume = ui.querySelector<HTMLInputElement>('.volume')!
    mute.addEventListener('click', () => {
      this.#audio.muted = !this.#audio.muted
    })
    volume.addEventListener('input', () => {
      this.#audio.volume = Number(volume.value)
      this.#audio.muted = this.#audio.volume === 0
    })
    this.#audio.addEventListener('volumechange', () => {
      const silent = this.#audio.muted || this.#audio.volume === 0
      mute.setAttribute('aria-label', silent ? 'Ton an' : 'Ton aus')
      mute.querySelector('span')!.className = silent ? 'msi-volume-off' : 'msi-volume-up'
      volume.value = String(this.#audio.muted ? 0 : this.#audio.volume)
    })

    this.addEventListener('keydown', (event) => {
      const target = event.target as HTMLElement
      if (event.altKey || event.ctrlKey || event.metaKey) return
      if (target.closest('input, select, summary, details')) return
      const actions: Record<string, () => void> = {
        j: () => this.#skip(-this.#skipBack),
        l: () => this.#skip(this.#skipForward),
        m: () => {
          this.#audio.muted = !this.#audio.muted
        },
      }
      const action = actions[event.key.toLowerCase()]
      if (!action) return
      event.preventDefault()
      action()
    })
  }

  #bindAudio(): void {
    const audio = this.#audio

    audio.addEventListener('play', () => this.#renderPlaying(true))
    audio.addEventListener('pause', () => {
      this.#renderPlaying(false)
      this.#storePosition(true)
    })
    audio.addEventListener('ended', () => {
      this.#renderPlaying(false)
      this.#clearPosition()
    })
    audio.addEventListener('seeked', () => this.#storePosition(true))
    window.addEventListener('pagehide', () => this.#storePosition(true))
    audio.addEventListener('waiting', () => this.classList.add('is-busy'))
    audio.addEventListener('playing', () => this.classList.remove('is-busy'))
    audio.addEventListener('canplay', () => this.classList.remove('is-busy'))
    audio.addEventListener('error', () => this.#showError())

    audio.addEventListener('loadedmetadata', () => {
      if (Number.isFinite(audio.duration) && audio.duration > 0) {
        this.#updateDuration(audio.duration)
      }
    })

    audio.addEventListener('timeupdate', () => {
      if (this.#scrubbing) return
      this.#renderTime(audio.currentTime)
      this.#highlightChapter(audio.currentTime)
      this.#storePosition()
      this.dispatchEvent(
        new CustomEvent('tw-player:time', {
          bubbles: true,
          detail: { playtime: audio.currentTime * 1000 },
        })
      )
    })
  }

  #renderPlaying(playing: boolean): void {
    this.classList.toggle('is-playing', playing)
    this.#toggle.setAttribute('aria-label', playing ? 'Pause' : 'Abspielen')
    this.#toggle.querySelector('span')!.className = playing ? 'msi-pause' : 'msi-play-arrow'
    this.#error.hidden = true
    if ('mediaSession' in navigator) {
      navigator.mediaSession.playbackState = playing ? 'playing' : 'paused'
    }
    this.dispatchEvent(new CustomEvent('tw-player:state', { bubbles: true, detail: { playing } }))
  }

  #updateDuration(seconds: number): void {
    this.#duration = seconds
    this.#seek.max = String(Math.floor(seconds))
    this.#total.textContent = formatClock(seconds)
    this.#renderTime(this.#audio.currentTime)
  }

  #renderTime(seconds: number): void {
    const position = clamp(seconds, 0, this.#duration || seconds)
    this.#current.textContent = formatClock(position)
    this.#seek.value = String(Math.floor(position))
    this.#seek.style.setProperty(
      '--progress',
      `${this.#duration > 0 ? (position / this.#duration) * 100 : 0}%`
    )

    const now = Date.now()
    if (now - this.#lastValuetext > VALUETEXT_INTERVAL_MS || this.#scrubbing) {
      this.#lastValuetext = now
      this.#seek.setAttribute(
        'aria-valuetext',
        `${formatSpoken(position)} von ${formatSpoken(this.#duration)}`
      )
    }
  }

  #highlightChapter(seconds: number): void {
    if (this.#chapters.length === 0) return
    let activeIndex = -1
    this.#chapters.forEach((chapter, index) => {
      if (seconds >= chapter.start) activeIndex = index
    })
    this.#chapterButtons.forEach((button, index) => {
      if (index === activeIndex) {
        button.setAttribute('aria-current', 'true')
      } else {
        button.removeAttribute('aria-current')
      }
    })
  }

  #skip(deltaSeconds: number): void {
    this.#seekTo(this.#audio.currentTime + deltaSeconds)
  }

  #seekTo(seconds: number): void {
    const limit = this.#duration > 0 ? this.#duration : Number.MAX_SAFE_INTEGER
    const target = clamp(seconds, 0, limit)
    this.#audio.currentTime = target
    this.#lastValuetext = 0
    this.#renderTime(target)
    this.#highlightChapter(target)
    this.#status.textContent = formatSpoken(target)
  }

  #setupDock(): void {
    this.setAttribute('role', 'region')
    this.setAttribute('aria-label', `Audioplayer: ${this.dataset.title ?? ''}`)

    if (!('IntersectionObserver' in window)) return

    this.#dockObserver = new IntersectionObserver(([entry]) => {
      this.#scrolledPast = !entry.isIntersecting && entry.boundingClientRect.bottom < 0
      this.#updateDock()
    })
    this.#dockObserver.observe(this)
    this.#audio.addEventListener('play', () => {
      this.#started = true
      this.#updateDock()
    })
  }

  #updateDock(): void {
    const allowed = this.dataset.dock !== 'started' || this.#started
    const shouldDock = this.#scrolledPast && allowed
    if (shouldDock === this.hasAttribute('data-docked')) return

    if (shouldDock) {
      this.style.minBlockSize = `${this.querySelector<HTMLElement>('.player-ui')!.offsetHeight}px`
      this.setAttribute('data-docked', '')
    } else {
      this.removeAttribute('data-docked')
      this.style.minBlockSize = ''
    }
  }

  get #storageKey(): string | null {
    const id = this.dataset.episodeId
    return id ? `${STORAGE_PREFIX}${id}` : null
  }

  #restorePosition(): void {
    const key = this.#storageKey
    if (!key) return
    try {
      const stored = Number(window.localStorage.getItem(key))
      const limit = this.#duration > 0 ? this.#duration - STORAGE_END_MARGIN_SECONDS : Infinity
      if (Number.isFinite(stored) && stored >= STORAGE_MIN_SECONDS && stored < limit) {
        this.#seekTo(stored)
        this.#status.textContent = `Fortgesetzt bei ${formatSpoken(stored)}`
      }
    } catch {
      /* storage unavailable */
    }
  }

  #storePosition(force = false): void {
    const key = this.#storageKey
    if (!key || this.#scrubbing || this.#audio.readyState === 0) return
    const now = Date.now()
    if (!force && now - this.#lastStored < STORAGE_INTERVAL_MS) return
    this.#lastStored = now
    const position = this.#audio.currentTime
    try {
      if (
        position < STORAGE_MIN_SECONDS ||
        position > this.#duration - STORAGE_END_MARGIN_SECONDS
      ) {
        window.localStorage.removeItem(key)
      } else {
        window.localStorage.setItem(key, String(Math.floor(position)))
      }
    } catch {
      /* storage unavailable */
    }
  }

  #clearPosition(): void {
    const key = this.#storageKey
    if (!key) return
    try {
      window.localStorage.removeItem(key)
    } catch {
      /* storage unavailable */
    }
  }

  #showError(): void {
    this.classList.remove('is-busy')
    this.#error.textContent = 'Die Audiodatei konnte nicht geladen werden.'
    this.#error.hidden = false
  }

  #setupMediaSession(): void {
    if (!('mediaSession' in navigator)) return
    const session = navigator.mediaSession
    const poster = this.dataset.poster
    session.metadata = new MediaMetadata({
      title: this.dataset.title ?? '',
      artist: this.dataset.show ?? '',
      album: this.dataset.show ?? '',
      artwork: poster ? [{ src: poster }] : [],
    })
    session.setActionHandler('play', () => void this.#audio.play())
    session.setActionHandler('pause', () => this.#audio.pause())
    session.setActionHandler('seekbackward', () => this.#skip(-this.#skipBack))
    session.setActionHandler('seekforward', () => this.#skip(this.#skipForward))
    session.setActionHandler('seekto', (details) => {
      if (details.seekTime !== undefined) this.#seekTo(details.seekTime)
    })
  }
}

export const initAudioPlayers = (): void => {
  if (!customElements.get('tw-audio-player')) {
    customElements.define('tw-audio-player', TwAudioPlayer)
  }
}
