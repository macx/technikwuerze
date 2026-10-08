/** FONTS */
import '@fontsource-variable/material-symbols-outlined/fill.css'

/* STYLES */
import './styles/main.css'

/* SCRIPTS */
import { initAudioPlayers } from './scripts/components/audio-player'
import { initCopyButtons } from './scripts/components/copy-button'
import { initHeaderNav } from './scripts/components/header-nav'
import { initSearchDialog } from './scripts/components/search-dialog'
import { initModeSwitch } from './scripts/components/theme-switch'
import { initViewTransitions } from './scripts/components/view-transitions'

/* Transcript */
import '@plugins/kirby-tw-transcript/assets/tw-transcript.css'
import { initDeepLink } from './scripts/components/deep-link'
import { initTranscriptSync } from './scripts/components/transcript-sync'
import { initTranscriptWords } from './scripts/components/transcript-words'

import { initKomments, openCommentFromHash } from './scripts/components/komments'

document.addEventListener('DOMContentLoaded', () => {
  initHeaderNav()
  initCopyButtons()
  initModeSwitch()
  initSearchDialog()
  initAudioPlayers()
  initTranscriptSync()
  initTranscriptWords()
  initDeepLink()
  initViewTransitions()
  initKomments()
  openCommentFromHash()

  if (document.querySelector('.tw-brand-networks')) {
    void import('./scripts/components/brand-networks').then(({ initBrandNetworks }) => {
      initBrandNetworks()
    })
  }
})
