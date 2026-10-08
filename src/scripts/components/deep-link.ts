import { buildTimeUrl, parseTimeHash } from './deep-link-time'
import { revealPlaytime } from './transcript-sync'

type AudioPlayerElement = HTMLElement & {
  playtime: number
  seek: (milliseconds: number) => void
  play: () => Promise<void>
}

const FEEDBACK_MS = 2_500

const getPlayer = (): AudioPlayerElement | null =>
  document.querySelector<AudioPlayerElement>('tw-audio-player')

const prefersReducedMotion = (): boolean =>
  window.matchMedia('(prefers-reduced-motion: reduce)').matches

const applyTimeHash = async (smooth: boolean): Promise<void> => {
  const seconds = parseTimeHash(location.hash)
  const player = getPlayer()
  if (seconds === null || !player) return

  await customElements.whenDefined('tw-audio-player')
  player.seek(seconds * 1000)

  const transcript = document.querySelector<HTMLDetailsElement>('details.tw-transcript')
  const behavior = smooth && !prefersReducedMotion() ? 'smooth' : 'auto'
  if (transcript) {
    transcript.open = true
    revealPlaytime(seconds * 1000, behavior)
    keepAlignedUntilScrolled(seconds * 1000)
  } else {
    player.scrollIntoView({ behavior, block: 'center' })
  }

  void player.play().catch(() => {})
}

const keepAlignedUntilScrolled = (playtime: number): void => {
  const events = ['wheel', 'touchmove', 'keydown', 'pointerdown'] as const
  const stop = (): void => {
    window.removeEventListener('load', realign)
    for (const name of events) window.removeEventListener(name, stop)
  }
  const realign = (): void => {
    revealPlaytime(playtime)
    stop()
  }

  if (document.readyState === 'complete') return
  window.addEventListener('load', realign)
  for (const name of events) window.addEventListener(name, stop, { passive: true })
}

const createLiveRegion = (): HTMLElement => {
  const region = document.createElement('p')
  region.className = 'sr-only'
  region.setAttribute('role', 'status')
  document.body.append(region)
  return region
}

const copyLink = async (url: string): Promise<boolean> => {
  try {
    await navigator.clipboard.writeText(url)
    return true
  } catch {
    return false
  }
}

const shareLink = async (url: string): Promise<boolean | null> => {
  const useSystemSheet = 'share' in navigator && window.matchMedia('(pointer: coarse)').matches
  if (!useSystemSheet) return copyLink(url)

  try {
    await navigator.share({ title: document.title, url })
    return null
  } catch (error) {
    return (error as DOMException).name === 'AbortError' ? null : copyLink(url)
  }
}

export const initDeepLink = (): void => {
  const liveRegion = createLiveRegion()

  document.addEventListener('click', async (event) => {
    const button = (event.target as HTMLElement).closest<HTMLButtonElement>('[data-share-current]')
    const player = getPlayer()
    if (!button || !player) return

    const seconds = player.playtime / 1000
    if (!Number.isFinite(seconds)) return

    const copied = await shareLink(buildTimeUrl(location, seconds))
    if (copied === null) return

    liveRegion.textContent = copied
      ? 'Link in die Zwischenablage kopiert'
      : 'Link konnte nicht kopiert werden'
    if (!copied) return

    const icon = button.querySelector('span')
    const tooltip = button.parentElement?.querySelector('.tooltip')
    if (!icon) return
    const tooltipText = tooltip?.textContent ?? ''
    icon.className = 'msi-check'
    if (tooltip) tooltip.textContent = 'Link kopiert'
    window.setTimeout(() => {
      icon.className = 'msi-share'
      if (tooltip) tooltip.textContent = tooltipText
    }, FEEDBACK_MS)
  })

  window.addEventListener('hashchange', () => void applyTimeHash(true))
  void applyTimeHash(false)
}
