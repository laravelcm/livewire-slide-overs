window.SlideOver = () => {
  return {
    open: false,
    showActiveComponent: true,
    activeComponent: false,
    closeOnEscape: true,
    componentHistory: [],
    panelWidth: null,
    panelPosition: 'right',
    stacked: false,
    listeners: [],
    getActiveComponentPanelAttribute(key) {
      if (this.$wire.get('components')[this.activeComponent] !== undefined) {
        return this.$wire.get('components')[this.activeComponent]['panelAttributes'][key]
      }
    },
    getComponentPanelAttribute(id, key) {
      const components = this.$wire.get('components')
      if (components[id] !== undefined) {
        return components[id]['panelAttributes'][key]
      }
    },
    isComponentVisible(id) {
      return id === this.activeComponent || this.componentHistory.includes(id)
    },
    getStackIndex(id) {
      if (id === this.activeComponent) {
        return 0
      }

      const historyIndex = this.componentHistory.indexOf(id)
      if (historyIndex === -1) {
        return -1
      }

      return this.componentHistory.length - historyIndex
    },
    getStackStyle(id) {
      const index = this.getStackIndex(id)
      if (index <= 0) {
        return {}
      }

      const position = this.getComponentPanelAttribute(id, 'position') ?? 'right'
      const dx = position === 'left' ? 1 : -1

      return {
        transform: 'scale(' + (1 - 0.05 * index) + ') translateX(' + (2 * dx * index) + 'rem)',
        opacity: index <= 2 ? 1 : 0,
      }
    },
    closePanelOnEscape(trigger) {
      if (this.getActiveComponentPanelAttribute('closeOnEscape') === false) {
        return
      }

      let force = this.getActiveComponentPanelAttribute('closeOnEscapeIsForceful') === true
      this.closePanel(force)
    },
    closePanelOnClickAway(trigger) {
      if (this.getActiveComponentPanelAttribute('closeOnClickAway') === false) {
        return
      }

      this.closePanel(true)
    },
    closePanel(force = false, skipPreviousPanels = 0, destroySkipped = false) {
      if (this.open === false) {
        return
      }

      if (this.getActiveComponentPanelAttribute('dispatchCloseEvent') === true) {
        const componentName = this.$wire.get('components')[this.activeComponent].name
        Livewire.dispatch('panelClosed', { name: componentName })
      }

      if (this.getActiveComponentPanelAttribute('destroyOnClose') === true) {
        Livewire.dispatch('destroyComponent', { id: this.activeComponent })
      }

      if (skipPreviousPanels > 0) {
        for (let i = 0; i < skipPreviousPanels; i++) {
          if (destroySkipped) {
            const id = this.componentHistory[this.componentHistory.length - 1]
            Livewire.dispatch('destroyComponent', { id: id })
          }
          this.componentHistory.pop()
        }
      }

      const id = this.componentHistory.pop()

      if (id && !force) {
        if (id) {
          this.setActivePanelComponent(id, true)
        } else {
          this.setShowPropertyTo(false)
        }
      } else {
        this.setShowPropertyTo(false)
      }
    },
    setActivePanelComponent(id, skip = false) {
      this.setShowPropertyTo(true)

      if (this.activeComponent === id) {
        return
      }

      if (this.activeComponent !== false && skip === false) {
        this.componentHistory.push(this.activeComponent)
      }

      let focusableTimeout = 50

      if (this.activeComponent === false) {
        this.activeComponent = id
        this.showActiveComponent = true
        this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass')
        this.panelPosition = this.getActiveComponentPanelAttribute('position') ?? 'right'
        this.closeOnEscape = this.getActiveComponentPanelAttribute('closeOnEscape') ?? true
      } else {
        if (!this.stacked) {
          this.showActiveComponent = false
        }

        focusableTimeout = 400

        setTimeout(() => {
          this.activeComponent = id
          this.showActiveComponent = true
          this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass')
          this.panelPosition = this.getActiveComponentPanelAttribute('position') ?? 'right'
          this.closeOnEscape = this.getActiveComponentPanelAttribute('closeOnEscape') ?? true
        }, this.stacked ? 0 : 300)
      }

      this.$nextTick(() => {
        let focusable = this.$refs[id]?.querySelector('[autofocus]')
        if (focusable) {
          setTimeout(() => {
            focusable.focus()
          }, focusableTimeout)
        }
      })
    },
    focusables() {
      let selector =
        "a, button, input:not([type='hidden']), textarea, select, details, [tabindex]:not([tabindex='-1'])"

      return [...this.$el.querySelectorAll(selector)].filter((el) => !el.hasAttribute('disabled'))
    },
    firstFocusable() {
      return this.focusables()[0]
    },
    lastFocusable() {
      return this.focusables().slice(-1)[0]
    },
    nextFocusable() {
      return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable()
    },
    prevFocusable() {
      return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable()
    },
    nextFocusableIndex() {
      return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1)
    },
    prevFocusableIndex() {
      return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1
    },
    setShowPropertyTo(open) {
      this.open = open

      if (open) {
        document.body.classList.add('overflow-y-hidden')
      } else {
        document.body.classList.remove('overflow-y-hidden')

        setTimeout(() => {
          this.activeComponent = false
          this.componentHistory = []
          this.$wire.resetState()
        }, 300)
      }
    },
    init() {
      this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass')
      this.panelPosition = this.getActiveComponentPanelAttribute('position') ?? 'right'
      this.stacked = this.$el.dataset.stacked === 'true'

      this.listeners.push(
        Livewire.on('closePanel', (data) => {
          this.closePanel(data?.force ?? false, data?.skipPreviousPanels ?? 0, data?.destroySkipped ?? false)
        }),
      )

      this.listeners.push(
        Livewire.on('activePanelComponentChanged', ({ id }) => {
          this.setActivePanelComponent(id)
        }),
      )
    },
    destroy() {
      this.listeners.forEach((listener) => {
        listener()
      })
    },
  }
}
