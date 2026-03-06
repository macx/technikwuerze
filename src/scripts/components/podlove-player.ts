type PodlovePayload = {
  config: unknown
  episode: unknown
  selector: string
  template: string | null
  transparent: boolean
  variant: string | null
}

type PodloveConfig = {
  theme?: {
    tokens?: Record<string, string>
    [key: string]: unknown
  }
  [key: string]: unknown
}

type PodlovePlayerFn = (selector: string, episode: unknown, config: unknown) => void

type PodloveWindow = Window & {
  podlovePlayer?: PodlovePlayerFn
  __twzPodloveState?: {
    activeMode: 'light' | 'dark'
    observer: IntersectionObserver | null
    payloads: Map<HTMLElement, PodlovePayload>
    scriptPromise: Promise<void> | null
  }
}

const PODLOVE_SCRIPT_SELECTOR = 'script[data-podlove-embed]'
const PODLOVE_SCRIPT_URL = 'https://cdn.podlove.org/web-player/5.x/embed.js'
const IFRAME_THEME_STYLE_ID = 'twz-podlove-theme'

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

const mountPlayer = (payload: PodlovePayload): void => {
  const win = window as PodloveWindow
  const element = document.querySelector<HTMLElement>(payload.selector)

  if (!element || element.dataset.podloveMounted === '1') {
    return
  }

  loadPodloveScript(win)
    .then(() => {
      if (!win.podlovePlayer || element.dataset.podloveMounted === '1') {
        return
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

      win.podlovePlayer(payload.selector, payload.episode, configForMount)
      element.dataset.podloveMounted = '1'
      applyIframeTheme(element, getActiveColorMode())
    })
    .catch(() => {
      // Keep the page stable if the external player script cannot be loaded.
    })
}

export const initPodlovePlayers = (): void => {
  const win = window as PodloveWindow

  if (!win.__twzPodloveState) {
    win.__twzPodloveState = {
      activeMode: getActiveColorMode(),
      observer: null,
      payloads: new Map(),
      scriptPromise: null,
    }
  }

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
