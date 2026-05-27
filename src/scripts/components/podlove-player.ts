type PodlovePayload = {
  config: unknown
  episode: unknown
  selector: string
  template: string | null
  transparent: boolean
  variant: string | null
  storePromise?: Promise<any>
}

type PodloveStore = {
  dispatch: (action: { type: string; payload?: unknown }) => void
  getState?: () => Record<string, unknown>
  subscribe?: (listener: () => void) => () => void
}

type TranscriptTimestampPoint = {
  button: HTMLButtonElement
  playtime: number
}

type PodloveConfig = {
  theme?: {
    tokens?: Record<string, string>
    [key: string]: unknown
  }
  [key: string]: unknown
}

type PodlovePlayerFn = (
  selector: string,
  episode: unknown,
  config: unknown
) => Promise<PodloveStore>

type PodloveWindow = Window & {
  podlovePlayer?: PodlovePlayerFn
  __twzPodloveState?: {
    activeMode: 'light' | 'dark'
    observer: IntersectionObserver | null
    payloads: Map<HTMLElement, PodlovePayload>
    stores: Map<HTMLElement, Promise<any>>
    scriptPromise: Promise<void> | null
  }
}

const PODLOVE_SCRIPT_SELECTOR = 'script[data-podlove-embed]'
const PODLOVE_SCRIPT_URL = 'https://cdn.podlove.org/web-player/5.x/embed.js'
const IFRAME_THEME_STYLE_ID = 'twz-podlove-theme'
const REQUEST_PLAYTIME = 'PLAYER_REQUEST_PLAYTIME'
const REQUEST_PLAY = 'PLAYER_REQUEST_PLAY'
const PODLOVE_STATE_PATHS: Array<Array<string>> = [
  ['playtime'],
  ['playtimeMs'],
  ['runtime', 'playtime'],
  ['runtime', 'playtimeMs'],
  ['player', 'playtime'],
  ['player', 'playtimeMs'],
  ['position'],
  ['runtime', 'position'],
  ['player', 'position'],
  ['currentTime'],
  ['runtime', 'currentTime'],
  ['player', 'currentTime'],
  ['timepiece', 'playtime'],
  ['timepiece', 'position'],
  ['time'],
  ['runtime', 'time'],
]
const transcriptSyncedStores = new WeakSet<PodloveStore>()
const MAX_AUTO_FOLLOW_JUMP_MS = 180_000
let lastActiveTranscriptPlaytime: number | null = null
let lastAutoFollowAt = 0
const AUTO_FOLLOW_MIN_INTERVAL_MS = 700
const MANUAL_SEEK_GLITCH_GUARD_MS = 20_000
const MANUAL_SEEK_NEAR_START_MS = 30_000
const END_OF_TRACK_TOLERANCE_MS = 1_500
const END_OF_TRACK_GLITCH_RATIO = 0.85
const END_OF_TRACK_GLITCH_MIN_JUMP_MS = 45_000
let lastManualTranscriptSeekAt = 0
let lastManualTranscriptSeekPlaytime: number | null = null

const DEFAULT_VISIBLE_COMPONENTS = [
  'poster',
  'showTitle',
  'episodeTitle',
  'progressbar',
  'controlSteppers',
  'controlChapters',
  'controlVolume',
  'chapters',
]

const parseJson = (value: string | undefined): unknown => {
  if (!value) {
    return null
  }

  try {
    return JSON.parse(value)
  } catch {
    return null
  }
}

const getEpisodeDateLabel = (episode: Record<string, unknown>): string => {
  const dateCandidates = [episode.publicationDate, episode.date, episode.published, episode.updated]

  for (const candidate of dateCandidates) {
    if (typeof candidate !== 'string' || candidate.trim() === '') {
      continue
    }

    const parsedDate = new Date(candidate)
    if (Number.isNaN(parsedDate.getTime())) {
      continue
    }

    return new Intl.DateTimeFormat('de-DE').format(parsedDate)
  }

  return ''
}

const getEpisodeSubtitleLabel = (episode: Record<string, unknown>): string => {
  const subtitleCandidates = [episode.subtitle, episode.episodeSubtitle]

  for (const candidate of subtitleCandidates) {
    if (typeof candidate === 'string' && candidate.trim() !== '') {
      return candidate.trim()
    }
  }

  return ''
}

const applyShowTitleOverride = (episode: unknown): unknown => {
  if (!episode || typeof episode !== 'object' || Array.isArray(episode)) {
    return episode
  }

  const episodeRecord = episode as Record<string, unknown>
  const showValue = episodeRecord.show
  if (!showValue || typeof showValue !== 'object' || Array.isArray(showValue)) {
    return episode
  }

  const showRecord = showValue as Record<string, unknown>
  const subtitle = getEpisodeSubtitleLabel(episodeRecord)
  if (subtitle === '') {
    return episode
  }

  const dateLabel = getEpisodeDateLabel(episodeRecord)
  const title = dateLabel !== '' ? `${subtitle} vom ${dateLabel}` : subtitle

  return {
    ...episodeRecord,
    show: {
      ...showRecord,
      title,
    },
  }
}

const getActiveColorMode = (): 'light' | 'dark' => {
  const mode = document.documentElement.getAttribute('mode')
  if (mode === 'light' || mode === 'dark') {
    return mode
  }

  const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false
  return prefersDark ? 'dark' : 'light'
}

const getThemeTokens = (mode: 'light' | 'dark'): Record<string, string> => {
  if (mode === 'dark') {
    return {
      brand: '#c6a3ff',
      brandDark: '#c6a3ff',
      brandDarkest: '#c6a3ff',
      brandLightest: 'hsl(0 0% 23%)',
      shadeBase: 'hsl(0 0% 23%)',
      shadeDark: 'rgba(255, 255, 255, 0.1)',
      contrast: '#ffffff',
      alt: '#ffffff',
    }
  }

  return {
    brand: '#6e33cc',
    brandDark: '#6e33cc',
    brandDarkest: '#6e33cc',
    brandLightest: '#fffcd1',
    shadeBase: '#fff894',
    shadeDark: 'rgba(0, 0, 0, 0.1)',
    contrast: '#2e2e2e',
    alt: '#2e2e2e',
  }
}

const applyThemeTokensFromCss = (config: unknown): unknown => {
  if (!config || typeof config !== 'object' || Array.isArray(config)) {
    return config
  }

  const podloveConfig = config as PodloveConfig

  if (
    !podloveConfig.theme ||
    typeof podloveConfig.theme !== 'object' ||
    Array.isArray(podloveConfig.theme)
  ) {
    podloveConfig.theme = {}
  }

  const existingTokens =
    podloveConfig.theme.tokens && typeof podloveConfig.theme.tokens === 'object'
      ? podloveConfig.theme.tokens
      : {}

  podloveConfig.theme.tokens = {
    ...existingTokens,
    ...getThemeTokens(getActiveColorMode()),
  }

  return podloveConfig
}

const loadPodloveScript = (win: PodloveWindow): Promise<void> => {
  if (win.podlovePlayer) {
    return Promise.resolve()
  }

  const state = win.__twzPodloveState
  if (!state) {
    return Promise.resolve()
  }

  if (state.scriptPromise) {
    return state.scriptPromise
  }

  state.scriptPromise = new Promise<void>((resolve, reject) => {
    let script = document.querySelector<HTMLScriptElement>(PODLOVE_SCRIPT_SELECTOR)

    if (!script) {
      script = document.createElement('script')
      script.src = PODLOVE_SCRIPT_URL
      script.dataset.podloveEmbed = '1'
      document.head.appendChild(script)
    }

    if (win.podlovePlayer) {
      resolve()
      return
    }

    script.addEventListener('load', () => resolve(), { once: true })
    script.addEventListener('error', () => reject(new Error('Podlove embed.js failed to load')), {
      once: true,
    })
  })

  return state.scriptPromise
}

const getIframeThemeStyle = (mode: 'light' | 'dark'): string => {
  const background = mode === 'dark' ? 'hsl(0 0% 23%)' : '#fffcd1'
  const contrast = mode === 'dark' ? '#ffffff' : '#2e2e2e'
  const divider = mode === 'dark' ? 'rgba(255, 255, 255, 0.2)' : 'rgba(0, 0, 0, 0.2)'

  return `
    #app,
    #app.loaded,
    #App,
    #App.loaded {
      opacity: 1 !important;
      transition: none !important;
      animation: none !important;
      background-color: ${background} !important;
      color: ${contrast} !important;
    }

    root {
      background-color: ${background} !important;
      color: ${contrast} !important;
    }

    divider {
      background-color: ${divider} !important;
    }

    .play-button,
    .play-button > div {
      min-width: 32px !important;
      width: 32px !important;
      height: 32px !important;
      min-height: 32px !important;
    }

    .show-title,
    show-title,
    [data-test='show-title'] {
      font-weight: 400 !important;
      color: ${contrast} !important;
    }

    [data-test='show-title-link'] {
      color: ${contrast} !important;
    }
  `
}

const patchIframeTheme = (iframe: HTMLIFrameElement, mode: 'light' | 'dark'): boolean => {
  let doc: Document | null = null
  try {
    doc = iframe.contentDocument
  } catch {
    return false
  }

  if (!doc) {
    return false
  }

  const root = doc.head ?? doc.documentElement
  if (!root) {
    return false
  }

  let style = doc.getElementById(IFRAME_THEME_STYLE_ID) as HTMLStyleElement | null
  if (!style) {
    style = doc.createElement('style')
    style.id = IFRAME_THEME_STYLE_ID
    root.appendChild(style)
  }
  style.textContent = getIframeThemeStyle(mode)

  const app = doc.getElementById('app') ?? doc.getElementById('App')
  if (app) {
    app.classList.add('loaded')
    ;(app as HTMLElement).style.opacity = '1'
    ;(app as HTMLElement).style.transition = 'none'
    ;(app as HTMLElement).style.animation = 'none'
    ;(app as HTMLElement).style.backgroundColor = mode === 'dark' ? 'hsl(0 0% 23%)' : '#fffcd1'
  }

  const dividerColor = mode === 'dark' ? 'rgba(255, 255, 255, 0.2)' : 'rgba(0, 0, 0, 0.2)'
  for (const divider of doc.querySelectorAll<HTMLElement>('divider')) {
    divider.style.backgroundColor = dividerColor
  }

  return true
}

const applyIframeTheme = (element: HTMLElement, mode: 'light' | 'dark'): void => {
  const patchIframe = (iframe: HTMLIFrameElement): void => {
    const runPatch = (): void => {
      let attempts = 0
      const maxAttempts = 180

      const tick = (): void => {
        attempts += 1
        if (patchIframeTheme(iframe, mode) || attempts >= maxAttempts) {
          return
        }
        requestAnimationFrame(tick)
      }

      requestAnimationFrame(tick)
    }

    if (iframe.dataset.twzThemeListener !== '1') {
      iframe.dataset.twzThemeListener = '1'
      iframe.addEventListener('load', () => runPatch())
    }

    runPatch()
  }

  for (const iframe of element.querySelectorAll<HTMLIFrameElement>('iframe')) {
    patchIframe(iframe)
  }

  if (element.dataset.twzIframeObserver !== '1') {
    element.dataset.twzIframeObserver = '1'

    const observer = new MutationObserver(() => {
      const currentMode = getActiveColorMode()
      for (const iframe of element.querySelectorAll<HTMLIFrameElement>('iframe')) {
        patchIframe(iframe)
        patchIframeTheme(iframe, currentMode)
      }
    })

    observer.observe(element, { childList: true, subtree: true })
    window.setTimeout(() => observer.disconnect(), 5000)
  }
}

const resetMountedPlayer = (element: HTMLElement): void => {
  element.removeAttribute('data-podlove-mounted')
  element.removeAttribute('data-twz-iframe-observer')

  while (element.firstChild) {
    element.removeChild(element.firstChild)
  }
}

const remountPlayersForTheme = (win: PodloveWindow): void => {
  const state = win.__twzPodloveState
  if (!state) {
    return
  }

  for (const [element, payload] of state.payloads.entries()) {
    if (!document.contains(element)) {
      state.payloads.delete(element)
      continue
    }

    const refreshedPayload: PodlovePayload = {
      ...payload,
      config: applyThemeTokensFromCss(parseJson(element.dataset.podloveConfig)),
    }
    state.payloads.set(element, refreshedPayload)

    if (element.dataset.podloveMounted === '1') {
      resetMountedPlayer(element)
      mountPlayer(refreshedPayload)
    }
  }
}

const getPrimaryPlayerStore = (): Promise<PodloveStore> | null => {
  const state = (window as PodloveWindow).__twzPodloveState
  if (!state || state.stores.size === 0) {
    return null
  }

  const firstEntry = state.stores.values().next()
  return firstEntry.done ? null : firstEntry.value
}

const getPrimaryPlayerHost = (): HTMLElement | null => {
  return document.querySelector<HTMLElement>('[data-podlove-player]')
}

const waitForPlayerReady = (store: PodloveStore): Promise<PodloveStore> => {
  const currentLifecycle = store.getState?.().lifecycle
  if (currentLifecycle === 'ready' || typeof store.subscribe !== 'function') {
    return Promise.resolve(store)
  }

  return new Promise((resolve) => {
    const unsubscribe = store.subscribe?.(() => {
      if (store.getState?.().lifecycle !== 'ready') {
        return
      }

      unsubscribe?.()
      resolve(store)
    })
  })
}

const nextAnimationFrame = (): Promise<void> => {
  return new Promise((resolve) => {
    window.requestAnimationFrame(() => resolve())
  })
}

const settlePlayerAfterReady = async (store: PodloveStore): Promise<PodloveStore> => {
  await Promise.resolve()
  await nextAnimationFrame()

  return store
}

const requestPlayerSeek = (store: PodloveStore, playtime: number): void => {
  store.dispatch({ type: REQUEST_PLAYTIME, payload: playtime })
}

const isSyncableTranscriptButton = (button: HTMLButtonElement): boolean => {
  const segment = button.closest<HTMLElement>('.segment')
  if (!segment) {
    return false
  }

  const content = segment.querySelector<HTMLElement>('.content')
  return (content?.textContent ?? '').trim() !== ''
}

const getTranscriptTimestampPoints = (syncableOnly = false): Array<TranscriptTimestampPoint> => {
  const points: Array<TranscriptTimestampPoint> = []

  for (const button of document.querySelectorAll<HTMLButtonElement>('.timestamp[data-timestamp]')) {
    if (syncableOnly && !isSyncableTranscriptButton(button)) {
      continue
    }

    const playtime = Number(button.dataset.timestamp)
    if (!Number.isFinite(playtime) || playtime < 0) {
      continue
    }

    points.push({ button, playtime })
  }

  points.sort((a, b) => a.playtime - b.playtime)
  return points
}

const getValueAtPath = (source: unknown, path: Array<string>): unknown => {
  let current: unknown = source

  for (const segment of path) {
    if (!current || typeof current !== 'object') {
      return undefined
    }

    if (typeof (current as { get?: (key: string) => unknown }).get === 'function') {
      current = (current as { get: (key: string) => unknown }).get(segment)
      continue
    }

    if (Array.isArray(current)) {
      return undefined
    }

    current = (current as Record<string, unknown>)[segment]
  }

  return current
}

const normalizePlaytimeToMilliseconds = (
  playtime: number,
  points: Array<TranscriptTimestampPoint>
): number => {
  const maxTimestamp = points.length > 0 ? points[points.length - 1].playtime : 0

  // Heuristic: transcript timestamps are in ms; if store value looks like seconds, convert.
  if (maxTimestamp >= 60_000 && playtime > 0 && playtime < maxTimestamp / 20) {
    return playtime * 1000
  }

  return playtime
}

const getStorePlaytime = (store: PodloveStore): number | null => {
  const rawState = store.getState?.()
  if (!rawState || typeof rawState !== 'object') {
    return null
  }

  const state =
    typeof (rawState as { toJS?: () => unknown }).toJS === 'function'
      ? (rawState as { toJS: () => unknown }).toJS()
      : rawState

  const points = getTranscriptTimestampPoints(true)
  const fallbackPoints = points.length > 0 ? points : getTranscriptTimestampPoints(false)
  const maxTimestamp = fallbackPoints[fallbackPoints.length - 1]?.playtime ?? 0

  for (const path of PODLOVE_STATE_PATHS) {
    const value = getValueAtPath(state, path)
    if (typeof value !== 'number' || Number.isFinite(value) !== true || value < 0) {
      continue
    }

    const normalized = normalizePlaytimeToMilliseconds(value, fallbackPoints)

    // Reject values that look like duration/metadata instead of current position.
    if (maxTimestamp > 0 && normalized > maxTimestamp + 30_000) {
      continue
    }

    return normalized
  }

  return null
}

const hasTranscriptTimestamps = (): boolean =>
  document.querySelector('.timestamp[data-timestamp]') !== null

const updateStickyPlayerAvailability = (): void => {
  const stickyPlayers = document.querySelectorAll<HTMLElement>('.episode-player-sticky')
  const enableSticky = hasTranscriptTimestamps()

  for (const stickyPlayer of stickyPlayers) {
    stickyPlayer.classList.toggle('is-sticky-enabled', enableSticky)
  }
}

const getStickyHeaderOffset = (): number => {
  const rootStyles = window.getComputedStyle(document.documentElement)
  const header = Number.parseFloat(rootStyles.getPropertyValue('--header-size')) || 0
  const content = Number.parseFloat(rootStyles.getPropertyValue('--sp-content')) || 0
  return Math.max(0, header + content)
}

const getStickyPlayerBottomOffset = (): number => {
  const headerOffset = getStickyHeaderOffset()
  const stickyPlayer = document.querySelector<HTMLElement>(
    '.episode-player-sticky.is-sticky-enabled'
  )
  if (!stickyPlayer) {
    return headerOffset
  }

  const rect = stickyPlayer.getBoundingClientRect()
  const stickyTop = Number.parseFloat(
    window.getComputedStyle(stickyPlayer).getPropertyValue('top') || '0'
  )

  // Only reserve player space while it is actually pinned at the top.
  if (Math.abs(rect.top - stickyTop) > 2 || rect.bottom <= headerOffset) {
    return headerOffset
  }

  // Cap reservation so large player heights can't force scroll target to page top.
  const cappedBottom = Math.min(rect.bottom, window.innerHeight * 0.55)
  return Math.max(headerOffset, cappedBottom)
}

const getTranscriptFollowOffset = (): number => getStickyPlayerBottomOffset() + 16

const shouldAutoFollowTranscript = (button: HTMLButtonElement): boolean => {
  const transcriptRoot = button.closest<HTMLElement>('.tw-transcript')
  if (!transcriptRoot) {
    return false
  }

  const rect = transcriptRoot.getBoundingClientRect()
  const viewportTop = getTranscriptFollowOffset()
  return rect.bottom > viewportTop && rect.top < window.innerHeight
}

const followActiveTranscriptEntry = (button: HTMLButtonElement): void => {
  if (!shouldAutoFollowTranscript(button)) {
    return
  }

  const now = Date.now()
  if (now - lastAutoFollowAt < AUTO_FOLLOW_MIN_INTERVAL_MS) {
    return
  }

  const segment = button.closest<HTMLElement>('.segment')
  if (!segment) {
    return
  }

  const stickyOffset = getTranscriptFollowOffset()
  const segmentTop = window.scrollY + segment.getBoundingClientRect().top
  const targetTop = Math.max(segmentTop - stickyOffset, 0)

  if (Math.abs(window.scrollY - targetTop) < 2) {
    return
  }

  window.scrollTo({ top: targetTop, behavior: 'smooth' })
  lastAutoFollowAt = now
}

const shouldFollowActiveChange = (activePlaytime: number): boolean => {
  if (lastActiveTranscriptPlaytime === null) {
    lastActiveTranscriptPlaytime = activePlaytime
    return false
  }

  const delta = activePlaytime - lastActiveTranscriptPlaytime
  lastActiveTranscriptPlaytime = activePlaytime

  if (delta <= 0) {
    return false
  }

  if (delta > MAX_AUTO_FOLLOW_JUMP_MS) {
    return false
  }

  return true
}

const shouldIgnoreManualSeekEndGlitch = (
  playtime: number,
  points: Array<TranscriptTimestampPoint>
): boolean => {
  if (lastManualTranscriptSeekPlaytime === null) {
    return false
  }

  const elapsedSinceManualSeek = Date.now() - lastManualTranscriptSeekAt
  if (elapsedSinceManualSeek > MANUAL_SEEK_GLITCH_GUARD_MS) {
    lastManualTranscriptSeekPlaytime = null
    return false
  }

  if (lastManualTranscriptSeekPlaytime > MANUAL_SEEK_NEAR_START_MS) {
    return false
  }

  const endPlaytime = points[points.length - 1]?.playtime ?? 0
  if (endPlaytime <= 0) {
    return false
  }

  const jumpFromManual = playtime - lastManualTranscriptSeekPlaytime
  if (jumpFromManual < END_OF_TRACK_GLITCH_MIN_JUMP_MS) {
    return false
  }

  const nearEndByTolerance = playtime >= endPlaytime - END_OF_TRACK_TOLERANCE_MS
  const nearEndByRatio = playtime >= endPlaytime * END_OF_TRACK_GLITCH_RATIO

  return nearEndByTolerance || nearEndByRatio
}

const syncActiveTranscriptTimestamp = (playtime: number): void => {
  const points = getTranscriptTimestampPoints(true)
  if (points.length === 0) {
    lastActiveTranscriptPlaytime = null
    return
  }

  const normalizedPlaytime = normalizePlaytimeToMilliseconds(playtime, points)

  if (shouldIgnoreManualSeekEndGlitch(normalizedPlaytime, points)) {
    return
  }

  let active = points[0]
  for (const point of points) {
    if (point.playtime > normalizedPlaytime) {
      break
    }

    active = point
  }

  const changed = setActiveTranscriptTimestamp(active.button)
  if (changed && shouldFollowActiveChange(active.playtime)) {
    followActiveTranscriptEntry(active.button)
  }
}

const syncTranscriptTimestampsWithStore = (store: PodloveStore): void => {
  if (transcriptSyncedStores.has(store) || typeof store.subscribe !== 'function') {
    return
  }

  transcriptSyncedStores.add(store)
  let initialPlaytimeSettled = false
  let suspiciousInitialIgnoredCount = 0

  let frameRequested = false
  const scheduleSync = () => {
    if (frameRequested) {
      return
    }

    frameRequested = true
    window.requestAnimationFrame(() => {
      frameRequested = false
      const playtime = getStorePlaytime(store)
      if (playtime === null) {
        return
      }

      if (!initialPlaytimeSettled) {
        const points = getTranscriptTimestampPoints(true)
        const maxPlaytime = points[points.length - 1]?.playtime ?? 0

        // Some players report duration first; skip a few of those frames.
        if (
          maxPlaytime > 0 &&
          playtime >= maxPlaytime - 1_000 &&
          suspiciousInitialIgnoredCount < 5
        ) {
          suspiciousInitialIgnoredCount += 1
          return
        }

        initialPlaytimeSettled = true
      }

      syncActiveTranscriptTimestamp(playtime)
    })
  }

  store.subscribe(scheduleSync)
  scheduleSync()
}

const ensurePrimaryPlayerStore = (): Promise<PodloveStore> | null => {
  const existingStore = getPrimaryPlayerStore()
  if (existingStore) {
    return existingStore
  }

  const win = window as PodloveWindow
  const state = win.__twzPodloveState
  if (!state || state.payloads.size === 0) {
    return null
  }

  const firstPayload = state.payloads.values().next()
  if (firstPayload.done) {
    return null
  }

  return mountPlayer(firstPayload.value)
}

const setActiveTranscriptTimestamp = (button: HTMLButtonElement): boolean => {
  let changed = false

  for (const timestampButton of document.querySelectorAll<HTMLButtonElement>('.timestamp')) {
    if (timestampButton === button) {
      if (
        timestampButton.dataset.playerActive !== '1' ||
        timestampButton.getAttribute('aria-current') !== 'true'
      ) {
        changed = true
      }

      timestampButton.dataset.playerActive = '1'
      timestampButton.setAttribute('aria-current', 'true')
      continue
    }

    if (
      timestampButton.dataset.playerActive === '1' ||
      timestampButton.getAttribute('aria-current') === 'true'
    ) {
      changed = true
    }

    delete timestampButton.dataset.playerActive
    timestampButton.removeAttribute('aria-current')
  }

  return changed
}

const initTranscriptTimestampLinks = (): void => {
  if (document.documentElement.dataset.twzTranscriptTimestampListener === '1') {
    return
  }

  document.addEventListener('click', (event) => {
    const target = event.target
    if (!(target instanceof HTMLElement)) {
      return
    }

    const button = target.closest<HTMLButtonElement>('.timestamp[data-timestamp]')
    if (!button) {
      return
    }

    const playtime = Number(button.dataset.timestamp)
    if (!Number.isFinite(playtime) || playtime < 0) {
      return
    }

    const storePromise = ensurePrimaryPlayerStore()
    if (!storePromise) {
      return
    }

    event.preventDefault()
    button.dataset.playerPending = '1'

    storePromise
      .then((store) => waitForPlayerReady(store))
      .then((store) => settlePlayerAfterReady(store))
      .then(async (store) => {
        if (!store || typeof store.dispatch !== 'function') {
          return
        }

        requestPlayerSeek(store, playtime)
        store.dispatch({ type: REQUEST_PLAY })
        await nextAnimationFrame()
        requestPlayerSeek(store, playtime)

        delete button.dataset.playerPending
        setActiveTranscriptTimestamp(button)
        lastActiveTranscriptPlaytime = playtime
        lastAutoFollowAt = Date.now()
        lastManualTranscriptSeekAt = Date.now()
        lastManualTranscriptSeekPlaytime = playtime

        const playerHost = getPrimaryPlayerHost()
        playerHost?.scrollIntoView({ behavior: 'smooth', block: 'start' })
      })
      .catch(() => {
        delete button.dataset.playerPending
        // Ignore player access errors and keep transcript links inert.
      })
  })

  document.documentElement.dataset.twzTranscriptTimestampListener = '1'
}

const mountPlayer = (payload: PodlovePayload): Promise<PodloveStore> | null => {
  const win = window as PodloveWindow
  const element = document.querySelector<HTMLElement>(payload.selector)

  if (!element) {
    return null
  }

  if (payload.storePromise) {
    return payload.storePromise
  }

  if (element.dataset.podloveMounted === '1') {
    return win.__twzPodloveState?.stores.get(element) ?? null
  }

  const storePromise = loadPodloveScript(win)
    .then(() => {
      if (!win.podlovePlayer || element.dataset.podloveMounted === '1') {
        return win.__twzPodloveState?.stores.get(element) ?? payload.storePromise ?? null
      }

      if (payload.variant) {
        element.dataset.variant = payload.variant
      }

      if (payload.template) {
        element.dataset.template = payload.template
      }

      let configForMount: unknown = payload.config
      if (payload.variant || payload.template || payload.transparent) {
        const configObject: Record<string, unknown> =
          payload.config && typeof payload.config === 'object' && !Array.isArray(payload.config)
            ? { ...(payload.config as Record<string, unknown>) }
            : {}

        if (payload.variant && configObject.variant === undefined) {
          configObject.variant = payload.variant
        }
        if (payload.template && configObject.template === undefined) {
          configObject.template = payload.template
        }
        if (payload.transparent && configObject.transparent === undefined) {
          configObject.transparent = true
        }

        const visibleComponents = Array.isArray(configObject.visibleComponents)
          ? [...(configObject.visibleComponents as unknown[])]
          : []
        if (visibleComponents.length === 0) {
          configObject.visibleComponents = [...DEFAULT_VISIBLE_COMPONENTS]
        } else if (!visibleComponents.includes('progressbar')) {
          visibleComponents.push('progressbar')
          configObject.visibleComponents = visibleComponents
        }

        configForMount = configObject
      }

      const mountedStorePromise = win.podlovePlayer(
        payload.selector,
        payload.episode,
        configForMount
      )
      void mountedStorePromise
        .then((store) => waitForPlayerReady(store))
        .then((store) => {
          syncTranscriptTimestampsWithStore(store)
          // Ensure Podlove sliders do not violate a11y focus rules
          setTimeout(() => {
            for (const input of element.querySelectorAll<HTMLInputElement>(
              'input[type="range"][aria-hidden="true"]'
            )) {
              input.removeAttribute('aria-hidden')
              input.setAttribute('tabindex', '-1')
            }
          }, 300)
        })
        .catch(() => {
          // Ignore store subscription errors and keep transcript links inert.
        })

      payload.storePromise = mountedStorePromise
      win.__twzPodloveState?.stores.set(element, mountedStorePromise)
      element.dataset.podloveMounted = '1'
      applyIframeTheme(element, getActiveColorMode())
      return mountedStorePromise
    })
    .catch(() => {
      // Keep the page stable if the external player script cannot be loaded.
      return Promise.reject(new Error('Podlove player mount failed'))
    })

  payload.storePromise = storePromise
  win.__twzPodloveState?.stores.set(element, storePromise)
  return storePromise
}

export const initPodlovePlayers = (): void => {
  const win = window as PodloveWindow

  if (!win.__twzPodloveState) {
    win.__twzPodloveState = {
      activeMode: getActiveColorMode(),
      observer: null,
      payloads: new Map(),
      stores: new Map(),
      scriptPromise: null,
    }
  }

  updateStickyPlayerAvailability()
  initTranscriptTimestampLinks()

  const entries = Array.from(document.querySelectorAll<HTMLElement>('[data-podlove-player]'))
  if (entries.length === 0) {
    return
  }

  const payloads = new Map<HTMLElement, PodlovePayload>()
  for (const element of entries) {
    if (!element.id) {
      continue
    }

    payloads.set(element, {
      selector: `#${element.id}`,
      config: applyThemeTokensFromCss(parseJson(element.dataset.podloveConfig)),
      episode: applyShowTitleOverride(parseJson(element.dataset.podloveEpisode)),
      variant: element.dataset.podloveVariant?.trim() || null,
      template: element.dataset.podloveTemplate?.trim() || null,
      transparent: element.dataset.podloveTransparent === '1',
    })
  }

  win.__twzPodloveState.payloads = payloads

  if (document.documentElement.dataset.podloveModeListener !== '1') {
    document.documentElement.addEventListener('twz:modechange', (event) => {
      const state = (window as PodloveWindow).__twzPodloveState
      if (!state) {
        return
      }

      const detail = (event as CustomEvent<{ effectiveMode?: 'light' | 'dark' }>).detail
      const nextMode = detail?.effectiveMode ?? getActiveColorMode()
      if (nextMode === state.activeMode) {
        return
      }

      state.activeMode = nextMode
      remountPlayersForTheme(window as PodloveWindow)
    })
    document.documentElement.dataset.podloveModeListener = '1'
  }

  if ('IntersectionObserver' in window) {
    const observer =
      win.__twzPodloveState.observer ??
      new IntersectionObserver(
        (observerEntries) => {
          for (const entry of observerEntries) {
            if (!entry.isIntersecting) {
              continue
            }

            const element = entry.target as HTMLElement
            const payload = payloads.get(element)
            if (payload) {
              mountPlayer(payload)
            }

            observer.unobserve(element)
          }
        },
        { rootMargin: '300px 0px' }
      )

    win.__twzPodloveState.observer = observer

    for (const element of payloads.keys()) {
      observer.observe(element)
    }

    return
  }

  for (const payload of payloads.values()) {
    mountPlayer(payload)
  }
}
