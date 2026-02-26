type PodlovePayload = {
  config: unknown
  debug: boolean
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
    modeObserver: MutationObserver | null
    observer: IntersectionObserver | null
    payloads: Map<HTMLElement, PodlovePayload>
    scriptPromise: Promise<void> | null
  }
}

const PODLOVE_SCRIPT_SELECTOR = 'script[data-podlove-embed]'
const PODLOVE_SCRIPT_URL = 'https://cdn.podlove.org/web-player/5.x/embed.js'
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

const getActiveColorMode = (): 'light' | 'dark' => {
  const mode = document.documentElement.getAttribute('mode')
  if (mode === 'light' || mode === 'dark') {
    return mode
  }

  const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false
  return prefersDark ? 'dark' : 'light'
}

const applyThemeTokensFromCss = (config: unknown): unknown => {
  if (!config || typeof config !== 'object' || Array.isArray(config)) {
    return config
  }

  const podloveConfig = config as PodloveConfig
  const mode = getActiveColorMode()

  const cssTokens: Record<string, string> =
    mode === 'dark'
      ? {
          brand: 'hsl(56 100% 79%)',
          brandDark: 'hsl(0, 0%, 100%)',
          brandDarkest: 'hsl(0, 0%, 100%)',
          brandLightest: 'hsl(0 0% 45%)',
          shadeBase: 'hsl(0 0% 45%)',
          shadeDark: 'hsl(0 0% 100% / 0.1)',
          contrast: 'hsl(0, 0%, 100%)',
          alt: 'hsl(0, 0%, 100%)',
        }
      : {
          brand: 'hsl(129 100% 26%)',
          brandDark: 'hsl(0, 0%, 18%)',
          brandDarkest: 'hsl(0, 0%, 18%)',
          brandLightest: 'hsl(56 100% 91%)',
          shadeBase: 'hsl(56 100% 91%)',
          shadeDark: 'hsl(0 0% 0% / 0.1)',
          contrast: 'hsl(0, 0%, 18%)',
          alt: 'hsl(0, 0%, 18%)',
        }

  if (Object.keys(cssTokens).length === 0) {
    return config
  }

  if (!podloveConfig.theme || typeof podloveConfig.theme !== 'object' || Array.isArray(podloveConfig.theme)) {
    podloveConfig.theme = {}
  }

  const existingTokens =
    podloveConfig.theme.tokens && typeof podloveConfig.theme.tokens === 'object'
      ? podloveConfig.theme.tokens
      : {}

  podloveConfig.theme.tokens = {
    ...existingTokens,
    ...cssTokens,
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

const resetMountedPlayer = (element: HTMLElement): void => {
  element.removeAttribute('data-podlove-mounted')
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
      if (payload.variant || payload.template) {
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

      if (payload.debug) {
        const logProgressState = (label: string): void => {
          const progress = element.querySelector('progress-bar,[data-test="progress-bar"]')
          const wrapper = progress?.parentElement ?? element.querySelector('progress-bar')?.parentElement
          console.debug('[podlove-debug]', label, {
            selector: payload.selector,
            hasProgressNode: Boolean(progress),
            progressTag: progress?.tagName ?? null,
            wrapperHtml: wrapper?.innerHTML ?? null,
          })
        }

        logProgressState('before-mount')
        queueMicrotask(() => logProgressState('after-microtask'))
        setTimeout(() => logProgressState('after-200ms'), 200)
        setTimeout(() => logProgressState('after-1200ms'), 1200)
      }

      win.podlovePlayer(payload.selector, payload.episode, configForMount)
      element.dataset.podloveMounted = '1'
    })
    .catch(() => {
      // Keep the page stable if the external player script cannot be loaded.
    })
}

export const initPodlovePlayers = (): void => {
  const win = window as PodloveWindow

  if (!win.__twzPodloveState) {
    win.__twzPodloveState = {
      modeObserver: null,
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
      debug: element.dataset.podloveDebug === '1',
      episode: parseJson(element.dataset.podloveEpisode),
      variant: element.dataset.podloveVariant?.trim() || null,
      template: element.dataset.podloveTemplate?.trim() || null,
      transparent: element.dataset.podloveTransparent === '1',
    })
  }

  win.__twzPodloveState.payloads = payloads

  if (!win.__twzPodloveState.modeObserver) {
    let rafId = 0
    const observer = new MutationObserver((mutations) => {
      const hasThemeMutation = mutations.some((mutation) => mutation.type === 'attributes')
      if (!hasThemeMutation) {
        return
      }

      if (rafId) {
        cancelAnimationFrame(rafId)
      }

      rafId = requestAnimationFrame(() => {
        remountPlayersForTheme(win)
      })
    })

    observer.observe(document.documentElement, {
      attributeFilter: ['mode'],
      attributes: true,
    })

    win.__twzPodloveState.modeObserver = observer
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
