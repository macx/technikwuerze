import {
  alignTokens,
  findActiveIndex,
  normalizeWord,
  selectWindow,
  type ArchiveWord,
} from './transcript-words-align'

type AudioPlayerElement = HTMLElement & { playtime: number; play: () => Promise<void> }

type WordsPayload = { t: Array<number>; w: Array<string> }

type TokenRange = { node: Text; start: number; end: number }

type Segment = {
  start: number
  content: HTMLElement
  ranges?: Array<TokenRange>
  times?: Array<number>
}

const STORAGE_KEY = 'tw-transcript:follow'
const HIGHLIGHT_NAME = 'twt-word'
const SEGMENT_END_FALLBACK_MS = 30_000

const readStoredFlag = (): boolean => {
  try {
    return window.localStorage.getItem(STORAGE_KEY) === '1'
  } catch {
    return false
  }
}

const storeFlag = (value: boolean): void => {
  try {
    window.localStorage.setItem(STORAGE_KEY, value ? '1' : '0')
  } catch {
    /* storage unavailable */
  }
}

const collectTokens = (
  content: HTMLElement
): { ranges: Array<TokenRange>; norms: Array<string> } => {
  const ranges: Array<TokenRange> = []
  const norms: Array<string> = []
  const walker = document.createTreeWalker(content, NodeFilter.SHOW_TEXT)

  for (let node = walker.nextNode(); node; node = walker.nextNode()) {
    const text = node.textContent ?? ''
    for (const match of text.matchAll(/\S+/g)) {
      const norm = normalizeWord(match[0])
      if (norm === '') continue
      ranges.push({ node: node as Text, start: match.index, end: match.index + match[0].length })
      norms.push(norm)
    }
  }

  return { ranges, norms }
}

export const initTranscriptWords = (): void => {
  const host = document.querySelector<HTMLElement>('[data-transcript-words]')
  const details = host?.querySelector<HTMLDetailsElement>('details.tw-transcript')
  const summary = details?.querySelector<HTMLElement>('summary')
  const player = document.querySelector<AudioPlayerElement>('tw-audio-player')

  if (!host || !details || !summary || !player || !('highlights' in CSS)) return

  const wordsUrl = host.dataset.transcriptWords!
  let following = readStoredFlag()
  let archive: Array<ArchiveWord> | null = null
  let segments: Array<Segment> = []
  let loading: Promise<boolean> | null = null
  let lastRange: Range | null = null
  let lastKey = ''
  let frame = 0

  const shell = document.createElement('div')
  shell.className = 'transcript-shell'
  details.before(shell)
  shell.append(details)

  const button = document.createElement('button')
  button.type = 'button'
  button.className = 'button button-compact transcript-follow'
  button.innerHTML = '<i class="msi-subtitles" aria-hidden="true"></i><span>Mitlesen</span>'
  button.setAttribute('aria-pressed', String(following))
  shell.append(button)

  const layoutButton = (): void => {
    const shellTop = shell.getBoundingClientRect().top
    const summaryBox = summary.getBoundingClientRect()
    shell.style.setProperty('--follow-y', `${summaryBox.top - shellTop + summaryBox.height / 2}px`)
    shell.style.setProperty('--follow-w', `${button.offsetWidth}px`)
  }
  new ResizeObserver(layoutButton).observe(shell)
  layoutButton()

  const clearHighlight = (): void => {
    CSS.highlights.delete(HIGHLIGHT_NAME)
    lastRange = null
    lastKey = ''
  }

  const loadWords = (): Promise<boolean> => {
    loading ??= fetch(wordsUrl)
      .then((response) =>
        response.ok ? (response.json() as Promise<WordsPayload>) : Promise.reject()
      )
      .then((payload) => {
        archive = payload.t.map((time, index) => ({ time, norm: normalizeWord(payload.w[index]) }))
        segments = [...details.querySelectorAll<HTMLElement>('.twt-segment')]
          .map((item) => ({
            start: Number(item.querySelector<HTMLElement>('.twt-timestamp')?.dataset.timestamp),
            content: item.querySelector<HTMLElement>('.twt-content'),
          }))
          .filter(
            (segment): segment is Segment =>
              segment.content !== null && Number.isFinite(segment.start)
          )
        return true
      })
      .catch(() => {
        button.disabled = true
        button.title = 'Mitlesen ist für diese Folge nicht verfügbar'
        following = false
        button.setAttribute('aria-pressed', 'false')
        return false
      })
    return loading
  }

  const alignSegment = (index: number): Segment => {
    const segment = segments[index]
    if (segment.times && segment.ranges) return segment

    const { ranges, norms } = collectTokens(segment.content)
    const next = segments[index + 1]?.start ?? null
    const windowWords = selectWindow(archive!, segment.start, next)
    segment.ranges = ranges
    segment.times = alignTokens(
      norms,
      windowWords,
      segment.start,
      next ?? segment.start + SEGMENT_END_FALLBACK_MS
    )
    return segment
  }

  const update = (): void => {
    if (!following || !archive || segments.length === 0) return

    const playtime = player.playtime
    const segmentIndex = findActiveIndex(
      segments.map((segment) => segment.start),
      playtime
    )
    if (segmentIndex < 0) return clearHighlight()

    const segment = alignSegment(segmentIndex)
    const tokenIndex = findActiveIndex(segment.times!, playtime)
    if (tokenIndex < 0) return clearHighlight()

    const key = `${segmentIndex}:${tokenIndex}`
    if (key === lastKey) return

    const token = segment.ranges![tokenIndex]
    lastRange = new Range()
    lastRange.setStart(token.node, token.start)
    lastRange.setEnd(token.node, token.end)
    CSS.highlights.set(HIGHLIGHT_NAME, new Highlight(lastRange))
    lastKey = key
  }

  const loop = (): void => {
    update()
    frame = window.requestAnimationFrame(loop)
  }

  const setFollowing = async (value: boolean): Promise<void> => {
    following = value
    button.setAttribute('aria-pressed', String(value))
    storeFlag(value)

    if (!value) return clearHighlight()
    if (await loadWords()) update()
  }

  const startPlayback = (): void => {
    if (!player.classList.contains('is-playing')) {
      void player.play().catch(() => {})
    }
  }

  button.addEventListener('click', () => {
    const enable = !details.open || !following
    details.open = true
    void setFollowing(enable)
    if (enable) startPlayback()
  })

  document.addEventListener('tw-player:time', update)
  document.addEventListener('tw-player:state', (event) => {
    window.cancelAnimationFrame(frame)
    if ((event as CustomEvent<{ playing: boolean }>).detail.playing) loop()
  })

  if (following) void loadWords().then(update)
}
