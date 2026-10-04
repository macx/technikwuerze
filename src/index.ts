/** FONTS */
import '@fontsource-variable/material-symbols-outlined/fill.css'

/* STYLES */
import './styles/main.css'

/* SCRIPTS */
import { initCopyButtons } from './scripts/components/copy-button'
import { initHeaderNav } from './scripts/components/header-nav'
import { initSearchDialog } from './scripts/components/search-dialog'
import { initModeSwitch } from './scripts/components/theme-switch'
import { initViewTransitions } from './scripts/components/view-transitions'

/* Podlove-Player */
// @ts-ignore - ignore missing types from composer-packages
import { initPodlovePlayers } from '@plugins/kirby-tw-transcript/assets/tw-transcript.js'
import '@plugins/kirby-tw-transcript/assets/tw-transcript.css'

import { initKomments, openCommentFromHash } from './scripts/components/komments'

document.addEventListener('DOMContentLoaded', () => {
  initHeaderNav()
  initCopyButtons()
  initModeSwitch()
  initSearchDialog()
  initPodlovePlayers()
  initViewTransitions()
  initKomments()
  openCommentFromHash()

  if (document.querySelector('.tw-brand-networks')) {
    void import('./scripts/components/brand-networks').then(({ initBrandNetworks }) => {
      initBrandNetworks()
    })
  }
})
