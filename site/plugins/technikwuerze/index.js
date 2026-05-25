const fallback = (value, text) => {
  if (typeof value === 'string' && value.trim() !== '') {
    return value.trim()
  }
  return text
}

const asList = (value) => {
  if (Array.isArray(value)) {
    return value.filter(Boolean)
  }

  if (typeof value === 'string' && value.trim() !== '') {
    return value
      .split(',')
      .map((entry) => entry.trim())
      .filter(Boolean)
  }

  return []
}

const structureCount = (value) => {
  if (Array.isArray(value)) {
    return value.length
  }
  return 0
}

panel.plugin('tw/brand', {
  blocks: {
    'brand-logo': {
      computed: {
        byline() {
          return fallback(this.content.byline, 'Animiertes Brand-Logo')
        },
      },
      template: `
        <div class="twz-block-preview">
          <p class="kicker">Brand-Logo</p>
          <h3 class="title">Technikwürze</h3>
          <p class="text">{{ byline }}</p>
        </div>
      `,
    },
    brand: {
      computed: {
        byline() {
          return fallback(this.content.byline, 'Brand-Emblem mit Netzwerken')
        },
        settings() {
          return asList(this.content.settings)
        },
      },
      template: `
        <div class="twz-block-preview">
          <p class="kicker">Brand-Emblem</p>
          <h3 class="title">Technikwürze</h3>
          <p class="text">{{ byline }}</p>
          <div class="chips" v-if="settings.length">
            <span class="chip" v-for="item in settings" :key="item">{{ item }}</span>
          </div>
        </div>
      `,
    },
    'podcast-networks': {
      computed: {
        start() {
          return fallback(this.content.listento_start, 'Jetzt auf')
        },
        end() {
          return fallback(this.content.listento_end, 'anhören')
        },
        defaultNetwork() {
          const labels = {
            rss: 'RSS Feed',
            overcast: 'Overcast',
            applepodcasts: 'Apple Podcasts',
            youtubemusic: 'YouTube Music',
            amazonmusic: 'Amazon Music',
            spotify: 'Spotify',
            pocketcasts: 'Pocket Casts',
            twitch: 'Twitch',
          }

          let selected = fallback(this.content.favorite_network, '').toLowerCase()
          if (
            selected === '' &&
            Array.isArray(this.content.networks) &&
            this.content.networks.length > 0
          ) {
            selected = String(this.content.networks[0]?.network ?? '').toLowerCase()
          }

          return labels[selected] ?? 'Spotify'
        },
        count() {
          return structureCount(this.content.networks)
        },
      },
      template: `
        <div class="twz-block-preview">
          <p class="kicker">Podcast-Netzwerke</p>
          <h3 class="title">{{ start }} {{ defaultNetwork }} {{ end }}</h3>
          <p class="meta">{{ count }} Einträge</p>
        </div>
      `,
    },
    'last-episode': {
      computed: {
        title() {
          return fallback(this.content.header, 'Aktuelle Folge')
        },
      },
      template: `
        <div class="twz-block-preview last-episode">
          <p class="kicker">Letzte Episode</p>
          <h3 class="title">{{ title }}</h3>
          <div class="player player-row">
            <span class="play" aria-hidden="true">▶</span>
            <div class="bars">
              <div class="bar"></div>
              <div class="bar"></div>
              <div class="bar"></div>
            </div>
          </div>
        </div>
      `,
    },
    'podcast-episodes': {
      computed: {
        title() {
          return fallback(this.content.headline, 'Podcast-Folgen')
        },
        source() {
          return fallback(this.content.source, 'latest')
        },
        amount() {
          return fallback(this.content.amount, '3')
        },
        colorScheme() {
          const value = fallback(this.content.color_scheme, 'secondary').toLowerCase()
          if (value === 'primary' || value === 'secondary') {
            return value
          }
          return 'secondary'
        },
      },
      template: `
        <div class="twz-block-preview podcast-episodes">
          <p class="kicker">Podcast-Folgen</p>
          <div class="title-row">
            <h3 class="title">{{ title }}</h3>
            <span class="chip">{{ source }} · {{ amount }} · {{ colorScheme }}</span>
          </div>
          <div class="episodes-grid">
            <div class="player player-row" v-for="i in 3" :key="i">
              <span class="play" aria-hidden="true">▶</span>
              <div class="bars">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
              </div>
            </div>
          </div>
        </div>
      `,
    },
    'podcast-stats': {
      computed: {
        title() {
          return fallback(this.content.headline, 'Podcast-Statistiken')
        },
      },
      template: `
        <div class="twz-block-preview podcast-stats">
          <p class="kicker">Podcast-Statistiken</p>
          <h3 class="title">{{ title }}</h3>
          <div class="stats-grid">
            <div class="stats-item" v-for="i in 4" :key="i">
              <span class="stats-icon">◉</span>
              <span class="stats-line label"></span>
              <span class="stats-line value" :class="'value-' + i"></span>
            </div>
          </div>
        </div>
      `,
    },
    teaser: {
      computed: {
        title() {
          return fallback(this.content.title, 'Teaser-Titel')
        },
        foreword() {
          return fallback(this.content.foreword, 'Vorwort-Text')
        },
        afterword() {
          return fallback(this.content.afterword, 'Nachwort-Text')
        },
        hasBadge() {
          return !!this.content.show_badge && !!this.content.badge_text
        },
        badgeText() {
          return fallback(this.content.badge_text, 'Badge')
        },
      },
      template: `
        <div class="twz-block-preview teaser">
          <p class="kicker">Teaser</p>
          <div class="title-row">
            <h3 class="title">{{ title }}</h3>
            <span class="badge" v-if="hasBadge">{{ badgeText }}</span>
          </div>
          <div class="columns">
            <p class="text">{{ foreword }}</p>
            <p class="text">{{ afterword }}</p>
          </div>
        </div>
      `,
    },
    'participants-list': {
      computed: {
        title() {
          return fallback(this.content.headline, 'Teilnehmende')
        },
        scopes() {
          return asList(this.content.participant_scope)
        },
      },
      template: `
        <div class="twz-block-preview">
          <p class="kicker">Teilnehmendenliste</p>
          <h3 class="title">{{ title }}</h3>
          <div class="chips" v-if="scopes.length">
            <span class="chip" v-for="item in scopes" :key="item">{{ item }}</span>
          </div>
        </div>
      `,
    },
    handwritten: {
      computed: {
        text() {
          return fallback(this.content.text, 'Handschriftlicher Text')
        },
      },
      template: `
        <div class="twz-block-preview">
          <p class="kicker">Handschrift</p>
          <h3 class="title">{{ text }}</h3>
        </div>
      `,
    },
    testimonials: {
      computed: {
        title() {
          return fallback(this.content.headline, 'Testimonials')
        },
        amount() {
          return this.content.amount || 2
        },
        order() {
          return fallback(this.content.order, 'random')
        },
      },
      template: `
        <div class="twz-block-preview testimonials">
          <p class="kicker">Testimonials</p>
          <h3 class="title">{{ title }}</h3>
          <div class="testimonials-grid">
            <article class="testimonial-card" v-for="i in 2" :key="i">
              <p class="testimonial-quote" aria-hidden="true">❝</p>
              <p class="testimonial-text" aria-hidden="true">
                <span class="testimonial-line"></span>
                <span class="testimonial-line"></span>
                <span class="testimonial-line short"></span>
              </p>
              <div class="testimonial-person">
                <span class="testimonial-avatar" aria-hidden="true"></span>
                <span class="testimonial-name-role" aria-hidden="true">
                  <span class="testimonial-line name"></span>
                  <span class="testimonial-line role"></span>
                </span>
              </div>
            </article>
          </div>
          <p class="meta">Anzahl: {{ amount }} · Reihenfolge: {{ order }}</p>
        </div>
      `,
    },
  },
  fields: {
    'tw-search-reindex': {
      data() {
        return {
          busy: false,
          message: '',
          tone: 'info',
        }
      },
      methods: {
        async reindex(scope) {
          if (this.busy) {
            return
          }

          this.busy = true
          this.tone = 'info'
          this.message = 'Reindex läuft…'

          try {
            const result = await this.$api.post(`tw-search/reindex/${scope}`)
            this.tone = 'positive'
            this.message = `${result.count} Dokumente (${scope}) neu indiziert. Gesamtindex: ${result.indexedTotal}.`
          } catch (error) {
            this.tone = 'negative'
            this.message = 'Reindex fehlgeschlagen. Bitte Logs prüfen.'
          } finally {
            this.busy = false
          }
        },
      },
      template: `
        <k-field class="k-tw-search-reindex-field">
          <k-headline tag="h4">Suche neu indizieren</k-headline>
          <k-button-group layout="collapsed">
            <k-button icon="refresh" variant="filled" theme="blue-icon" :disabled="busy" @click="reindex('all')">Alles</k-button>
            <k-button icon="refresh" variant="filled" :disabled="busy" @click="reindex('content')">Inhalte</k-button>
            <k-button icon="refresh" variant="filled" :disabled="busy" @click="reindex('episode')">Episoden</k-button>
            <k-button icon="refresh" variant="filled" :disabled="busy" @click="reindex('participant')">Teilnehmende</k-button>
            <k-button icon="refresh" variant="filled" :disabled="busy" @click="reindex('comment')">Kommentare</k-button>
          </k-button-group>
          <k-box v-if="message" :theme="tone" style="margin-top: var(--spacing-3);">{{ message }}</k-box>
        </k-field>
      `,
    },
  },
})
