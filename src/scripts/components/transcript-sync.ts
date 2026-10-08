type TimestampPoint = { button: HTMLButtonElement; playtime: number }

type AudioPlayerElement = HTMLElement & {
  seek: (milliseconds: number) => void
  play: () => Promise<void>
}

const AUTO_FOLLOW_MIN_INTERVAL_MS = 700
const MAX_AUTO_FOLLOW_JUMP_MS = 180_000
const FOLLOW_MARGIN_PX = 24

let lastFollowAt = 0
let lastActivePlaytime: number | null = null

const getPlayer = (): AudioPlayerElement | null =>
  document.querySelector<AudioPlayerElement>('tw-audio-player')

const isSyncable = (button: HTMLButtonElement): boolean => {
  const content = button.closest('.twt-segment')?.querySelector('.twt-content')
  return (content?.textContent ?? '').trim() !== ''
}

const getPoints = (): Array<TimestampPoint> => {
  const points: Array<TimestampPoint> = []

  for (const button of document.querySelectorAll<HTMLButtonElement>(
    '.twt-timestamp[data-timestamp]'
  )) {
    const playtime = Number(button.dataset.timestamp)
    if (Number.isFinite(playtime) && playtime >= 0 && isSyncable(button)) {
      points.push({ button, playtime })
    }
  }

  return points.sort((a, b) => a.playtime - b.playtime)
}

const setActive = (button: HTMLButtonElement): boolean => {
  let changed = false

  for (const candidate of document.querySelectorAll<HTMLButtonElement>('.twt-timestamp')) {
    const isActive = candidate === button
    if ((candidate.getAttribute('aria-current') === 'true') !== isActive) {
      changed = true
    }

    if (isActive) {
      candidate.dataset.playerActive = '1'
      candidate.setAttribute('aria-current', 'true')
    } else {
      delete candidate.dataset.playerActive
      candidate.removeAttribute('aria-current')
    }
  }

  return changed
}

const getFollowOffset = (): number => {
  const header = document.querySelector<HTMLElement>('.main-header')
  return (header?.getBoundingClientRect().bottom ?? 0) + FOLLOW_MARGIN_PX
}

const follow = (button: HTMLButtonElement): void => {
  const transcript = button.closest<HTMLElement>('.tw-transcript')
  const segment = button.closest<HTMLElement>('.twt-segment')
  if (!transcript || !segment) return

  const rect = transcript.getBoundingClientRect()
  if (rect.bottom <= getFollowOffset() || rect.top >= window.innerHeight) return

  const now = Date.now()
  if (now - lastFollowAt < AUTO_FOLLOW_MIN_INTERVAL_MS) return

  const top = Math.max(window.scrollY + segment.getBoundingClientRect().top - getFollowOffset(), 0)
  if (Math.abs(window.scrollY - top) < 2) return

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  window.scrollTo({ top, behavior: reduceMotion ? 'auto' : 'smooth' })
  lastFollowAt = now
}

const findActivePoint = (points: Array<TimestampPoint>, playtime: number): TimestampPoint => {
  let active = points[0]
  for (const point of points) {
    if (point.playtime > playtime) break
    active = point
  }
  return active
}

export const revealPlaytime = (
  playtime: number,
  behavior: ScrollBehavior = 'auto'
): HTMLButtonElement | null => {
  const points = getPoints()
  if (points.length === 0) return null

  const active = findActivePoint(points, playtime)
  setActive(active.button)
  lastActivePlaytime = active.playtime
  lastFollowAt = Date.now()

  const segment = active.button.closest<HTMLElement>('.twt-segment')
  if (segment) {
    const top = Math.max(
      window.scrollY + segment.getBoundingClientRect().top - getFollowOffset(),
      0
    )
    window.scrollTo({ top, behavior })
  }
  return active.button
}

const syncActive = (playtime: number): void => {
  const points = getPoints()
  if (points.length === 0) return

  const active = findActivePoint(points, playtime)

  const changed = setActive(active.button)
  const previous = lastActivePlaytime
  lastActivePlaytime = active.playtime

  if (!changed || previous === null) return
  const delta = active.playtime - previous
  if (delta > 0 && delta <= MAX_AUTO_FOLLOW_JUMP_MS) {
    follow(active.button)
  }
}

export const initTranscriptSync = (): void => {
  document.addEventListener('click', (event) => {
    const button = (event.target as HTMLElement).closest<HTMLButtonElement>(
      '.twt-timestamp[data-timestamp]'
    )
    const playtime = Number(button?.dataset.timestamp)
    const player = getPlayer()
    if (!button || !player || !Number.isFinite(playtime) || playtime < 0) return

    event.preventDefault()
    void customElements.whenDefined('tw-audio-player').then(() => {
      player.seek(playtime)
      void player.play().catch(() => {})
      setActive(button)
      lastActivePlaytime = playtime
      lastFollowAt = Date.now()
    })
  })

  document.addEventListener('tw-player:time', (event) => {
    syncActive((event as CustomEvent<{ playtime: number }>).detail.playtime)
  })
}
