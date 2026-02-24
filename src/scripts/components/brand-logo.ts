const MAX_FONT_VARIATION = "'wght' 620, 'wdth' 104, 'opsz' 58"

const clamp = (value: number, min: number, max: number): number => {
  return Math.min(max, Math.max(min, value))
}

const getRemSize = (): number => {
  const rootSize = Number.parseFloat(
    globalThis.getComputedStyle(document.documentElement).fontSize
  )

  return Number.isFinite(rootSize) && rootSize > 0 ? rootSize : 16
}

export const initBrandLogo = (): void => {
  const blocks = document.querySelectorAll<HTMLElement>('.tw-brand')

  for (const block of blocks) {
    const animation = block.querySelector<HTMLElement>('.animation')
    const words = animation?.querySelectorAll<HTMLElement>('.word')

    if (!animation || !words || words.length === 0) {
      continue
    }

    const measure = animation.cloneNode(true) as HTMLElement
    measure.setAttribute('aria-hidden', 'true')
    measure.style.position = 'absolute'
    measure.style.inset = '0 auto auto 0'
    measure.style.visibility = 'hidden'
    measure.style.pointerEvents = 'none'
    measure.style.whiteSpace = 'nowrap'
    measure.style.inlineSize = 'max-content'
    measure.style.fontSize = '100px'

    const measureWords = measure.querySelectorAll<HTMLElement>('.word')
    for (const word of measureWords) {
      word.style.animation = 'none'
      word.style.fontVariationSettings = MAX_FONT_VARIATION
    }

    block.append(measure)

    let rafId = 0
    const updateSize = (): void => {
      rafId = 0

      const availableWidth = block.clientWidth
      const measuredWidth = measure.getBoundingClientRect().width

      if (availableWidth <= 0 || measuredWidth <= 0) {
        return
      }

      const rem = getRemSize()
      const minSize = rem * 3.2
      const maxSize = rem * 8
      const nextSize = clamp((availableWidth / measuredWidth) * 100 * 0.96, minSize, maxSize)
      const nextWidth = (measuredWidth * nextSize) / 100

      block.style.setProperty('--brand-logo-size', `${nextSize}px`)
      block.style.setProperty('--brand-logo-width', `${nextWidth}px`)
    }

    const requestUpdate = (): void => {
      if (rafId !== 0) {
        return
      }

      rafId = globalThis.requestAnimationFrame(updateSize)
    }

    const resizeObserver = new ResizeObserver(() => {
      requestUpdate()
    })

    resizeObserver.observe(block)
    requestUpdate()

    void document.fonts.ready.then(() => {
      requestUpdate()
    })
  }
}
