type PodlovePayload = {
  config: unknown
  episode: unknown
  selector: string
}

type PodlovePlayerFn = (selector: string, episode: unknown, config: unknown) => void

type PodloveWindow = Window & {
  podlovePlayer?: PodlovePlayerFn
  __twzPodloveState?: {
    observer: IntersectionObserver | null
    scriptPromise: Promise<void> | null
  }
}

const PODLOVE_SCRIPT_SELECTOR = 'script[data-podlove-embed]'
const PODLOVE_SCRIPT_URL = 'https://cdn.podlove.org/web-player/5.x/embed.js'

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

      win.podlovePlayer(payload.selector, payload.episode, payload.config)
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
      observer: null,
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
      config: parseJson(element.dataset.podloveConfig),
      episode: parseJson(element.dataset.podloveEpisode),
    })
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
