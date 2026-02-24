export const initBrandNetworks = (): void => {
  const blocks = document.querySelectorAll<HTMLElement>('.tw-brand-networks')

  for (const block of blocks) {
    const pointerLabel = block.querySelector<HTMLElement>('.pointer-network')
    const pointerText = block.querySelector<HTMLElement>('.pointer-text')
    const pointerCustom = block.querySelector<HTMLElement>('.pointer-custom')
    const pointer = block.querySelector<HTMLElement>('.pointer')
    const links = block.querySelectorAll<HTMLAnchorElement>('a[data-network-label]')

    if (!pointerLabel || !pointerText || !pointerCustom || !pointer || links.length === 0) {
      continue
    }

    const linksArray = Array.from(links)
    const defaultLink =
      linksArray.find((link) => link.classList.contains('is-pointer-target')) ?? linksArray[0]
    const firstLabel = defaultLink.dataset.networkLabel?.trim()
    const firstNetworkKey = defaultLink.dataset.networkKey?.trim()
    const firstRssText = defaultLink.dataset.rssText?.trim()
    const defaultLabel =
      firstLabel && firstLabel.length > 0 ? firstLabel : (pointerLabel.textContent?.trim() ?? '')
    const secondHalfStart = Math.floor(linksArray.length / 2)
    const linkIndices = new Map<HTMLAnchorElement, number>(
      linksArray.map((link, index) => [link, index])
    )

    const setLabel = (label: string): void => {
      pointerLabel.textContent = label
    }

    const showStandardText = (label: string): void => {
      pointerCustom.textContent = ''
      pointerText.classList.remove('is-rss-custom')
      setLabel(label)
    }

    const showRssCustomText = (text: string): void => {
      pointerCustom.textContent = text
      pointerText.classList.add('is-rss-custom')
    }

    const setArrowSideByLink = (link: HTMLAnchorElement): void => {
      const index = linkIndices.get(link) ?? 0
      pointer.classList.toggle('is-left', index >= secondHalfStart)
    }

    let currentPointerTarget: HTMLAnchorElement | null = null
    const setPointerTarget = (target: HTMLAnchorElement): void => {
      if (currentPointerTarget === target) {
        return
      }

      currentPointerTarget?.classList.remove('is-pointer-target')
      target.classList.add('is-pointer-target')
      currentPointerTarget = target
    }

    const resetToDefault = (): void => {
      if (firstNetworkKey === 'rss' && firstRssText) {
        showRssCustomText(firstRssText)
      } else {
        showStandardText(defaultLabel)
      }

      setArrowSideByLink(defaultLink)
      setPointerTarget(defaultLink)
    }

    const updatePointerFromLink = (link: HTMLAnchorElement): void => {
      const networkKey = link.dataset.networkKey?.trim()
      const rssText = link.dataset.rssText?.trim()
      const label = link.dataset.networkLabel?.trim()

      if (networkKey === 'rss' && rssText) {
        showRssCustomText(rssText)
      } else if (label) {
        showStandardText(label)
      }

      setArrowSideByLink(link)
      setPointerTarget(link)
    }

    const copyTextToClipboard = async (text: string): Promise<boolean> => {
      if (!text) {
        return false
      }

      if (navigator.clipboard?.writeText) {
        try {
          await navigator.clipboard.writeText(text)
          return true
        } catch {
          // Continue with fallback below.
        }
      }

      const textarea = document.createElement('textarea')
      textarea.value = text
      textarea.setAttribute('readonly', '')
      textarea.style.position = 'fixed'
      textarea.style.inset = '-9999px'
      document.body.append(textarea)
      textarea.select()

      let copied = false
      try {
        copied = document.execCommand('copy')
      } finally {
        textarea.remove()
      }

      return copied
    }

    let resetTimer: ReturnType<typeof globalThis.setTimeout> | null = null

    const clearResetTimer = (): void => {
      if (resetTimer !== null) {
        globalThis.clearTimeout(resetTimer)
        resetTimer = null
      }
    }

    const showPointer = (): void => {
      pointer.classList.remove('is-copy-hide')
    }

    const triggerCopyHide = (): void => {
      pointer.classList.remove('is-copy-hide')
      // Restart CSS animation reliably on repeated clicks.
      void pointer.offsetWidth
      pointer.classList.add('is-copy-hide')
    }

    const scheduleReset = (): void => {
      clearResetTimer()
      // Wait until pointer fade-out finished, so there is no visible jump to default.
      resetTimer = globalThis.setTimeout(() => {
        if (!block.matches(':hover') && !block.contains(document.activeElement)) {
          resetToDefault()
        }
        resetTimer = null
      }, 260)
    }

    resetToDefault()

    const onLinkActivate = (link: HTMLAnchorElement): void => {
      showPointer()
      clearResetTimer()
      updatePointerFromLink(link)
    }

    for (const link of linksArray) {
      link.addEventListener('pointerenter', () => {
        onLinkActivate(link)
      })

      link.addEventListener('focus', () => {
        onLinkActivate(link)
      })

      link.addEventListener('click', async (event) => {
        if (link.dataset.networkKey?.trim() !== 'rss') {
          return
        }

        event.preventDefault()
        const copied = await copyTextToClipboard(link.href)

        if (copied) {
          const copiedText = link.dataset.rssTextCopied?.trim()
          const hoverText = link.dataset.rssText?.trim()
          showRssCustomText(copiedText || hoverText || 'Copied')
          triggerCopyHide()
        }
      })
    }

    block.addEventListener('pointerleave', () => {
      scheduleReset()
    })

    block.addEventListener('pointerenter', () => {
      showPointer()
      clearResetTimer()
    })

    block.addEventListener('focusout', () => {
      queueMicrotask(() => {
        if (!block.contains(document.activeElement)) {
          scheduleReset()
        }
      })
    })
  }
}
