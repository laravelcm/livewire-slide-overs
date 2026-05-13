// === Common ===

const slideOverCommon = {
  open: false,
  activeComponent: false,
  componentHistory: [],
  closeOnEscape: true,
  panelWidth: null,
  panelPosition: 'right',
  showActiveComponent: true,
  listeners: [],
  optimisticComponents: {},
  optimisticTimeouts: {},
  resizedComponents: {},
  optimisticTimeoutMs: 10000,
  cacheKey: 'livewire-slide-over-cache',
  _closeCleanupTimeout: null,

  cancelCloseCleanup() {
    if (this._closeCleanupTimeout) {
      clearTimeout(this._closeCleanupTimeout)
      this._closeCleanupTimeout = null
    }
  },

  readCache() {
    try {
      return JSON.parse(window.sessionStorage.getItem(this.cacheKey) || '{}')
    } catch (e) {
      return {}
    }
  },
  writeCache(component, panelAttributes) {
    try {
      const cache = this.readCache()
      cache[component] = panelAttributes
      window.sessionStorage.setItem(this.cacheKey, JSON.stringify(cache))
    } catch (e) {}
  },
  stableStringify(value) {
    if (value === null || typeof value !== 'object') return JSON.stringify(value)
    if (Array.isArray(value)) return '[' + value.map((v) => this.stableStringify(v)).join(',') + ']'
    const keys = Object.keys(value).sort()
    return '{' + keys.map((k) => JSON.stringify(k) + ':' + this.stableStringify(value[k])).join(',') + '}'
  },
  deriveId(name, args) {
    const str = name + ':' + this.stableStringify(args ?? {})
    let fnv = 0x811c9dc5
    let djb = 5381
    for (let i = 0; i < str.length; i++) {
      const c = str.charCodeAt(i)
      fnv = Math.imul(fnv ^ c, 0x01000193)
      djb = ((djb << 5) + djb + c) | 0
    }
    return 'so-' + (fnv >>> 0).toString(36) + '-' + (djb >>> 0).toString(36)
  },
  getPanelState(id) {
    if (id === this.activeComponent) return 'active'
    if (this.componentHistory.includes(id)) return 'behind'
    return 'ahead'
  },
  getActiveComponentPanelAttribute(key) {
    return this.getComponentPanelAttribute(this.activeComponent, key)
  },
  getComponentPanelAttribute(id, key) {
    if (key === 'maxWidthClass' && this.resizedComponents[id] !== undefined) {
      return this.resizedComponents[id]
    }
    if (this.optimisticComponents[id] !== undefined) {
      return this.optimisticComponents[id]['panelAttributes'][key]
    }
    const components = this.$wire.get('components')
    if (components[id] !== undefined) {
      return components[id]['panelAttributes'][key]
    }
  },
  scheduleOptimisticTimeout(id) {
    this.clearOptimisticTimeout(id)
    this.optimisticTimeouts[id] = setTimeout(() => {
      if (this.optimisticComponents[id]) {
        delete this.optimisticComponents[id]
        if (this.activeComponent === id) this.closePanel(true)
      }
      delete this.optimisticTimeouts[id]
    }, this.optimisticTimeoutMs)
  },
  clearOptimisticTimeout(id) {
    if (this.optimisticTimeouts[id]) {
      clearTimeout(this.optimisticTimeouts[id])
      delete this.optimisticTimeouts[id]
    }
  },
  openOptimistically(component, args, attrs, id) {
    const cached = this.readCache()[component]
    if (!cached && Object.keys(attrs).length === 0) {
      Livewire.dispatch('openPanel', { component, arguments: args, panelAttributes: attrs, id })
      return
    }
    this.optimisticComponents[id] = {
      name: component,
      arguments: args,
      panelAttributes: Object.assign({}, cached || {}, attrs),
    }
    this.scheduleOptimisticTimeout(id)
    this.setActivePanelComponent(id)
    Livewire.dispatch('openPanel', { component, arguments: args, panelAttributes: attrs, id })
  },
  closePanelOnEscape() {
    if (this.getActiveComponentPanelAttribute('closeOnEscape') === false) return
    const force = this.getActiveComponentPanelAttribute('closeOnEscapeIsForceful') === true
    if (this.componentHistory.length > 0) {
      this.closePanel(false)
      return
    }
    this.closePanel(force)
  },
  closePanelOnClickAway() {
    if (this.getActiveComponentPanelAttribute('closeOnClickAway') === false) return
    this.closePanel(true)
  },
  closePanel(force = false, skipPreviousPanels = 0, destroySkipped = false) {
    if (this.open === false) return
    if (this.getActiveComponentPanelAttribute('dispatchCloseEvent') === true) {
      const serverComponents = this.$wire.get('components')
      const componentName = serverComponents[this.activeComponent]?.name
        ?? this.optimisticComponents[this.activeComponent]?.name
      if (componentName) {
        Livewire.dispatch('panelClosed', { name: componentName })
      }
    }
    if (this.getActiveComponentPanelAttribute('destroyOnClose') === true) {
      Livewire.dispatch('destroyComponent', { id: this.activeComponent })
    }
    if (skipPreviousPanels > 0) {
      for (let i = 0; i < skipPreviousPanels; i++) {
        if (destroySkipped) {
          const id = this.componentHistory[this.componentHistory.length - 1]
          Livewire.dispatch('destroyComponent', { id })
        }
        this.componentHistory.pop()
      }
    }
    const id = this.componentHistory.pop()
    if (id && !force) {
      this.setActivePanelComponent(id, true)
    } else {
      this.setShowPropertyTo(false)
    }
  },
  focusables() {
    const selector = "a, button, input:not([type='hidden']), textarea, select, details, [tabindex]:not([tabindex='-1'])"
    return [...this.$el.querySelectorAll(selector)].filter((el) => !el.hasAttribute('disabled'))
  },
  firstFocusable() { return this.focusables()[0] },
  lastFocusable() { return this.focusables().slice(-1)[0] },
  nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
  prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
  nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
  prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
  setupListeners() {
    this.listeners.push(
      Livewire.on('closePanel', (data) => {
        this.closePanel(data?.force ?? false, data?.skipPreviousPanels ?? 0, data?.destroySkipped ?? false)
      }),
    )
    this.listeners.push(
      Livewire.on('activePanelComponentChanged', ({ id }) => {
        this.clearOptimisticTimeout(id)
        if (!this.open && id in this.optimisticComponents) {
          delete this.optimisticComponents[id]
          Livewire.dispatch('destroyComponent', { id })
          return
        }
        delete this.optimisticComponents[id]
        this.setActivePanelComponent(id)
        const server = this.$wire.get('components')[id]
        if (server) {
          this.writeCache(server.name, server.panelAttributes)
          this.panelWidth = this.resizedComponents[id] ?? server.panelAttributes.maxWidthClass
          this.panelPosition = server.panelAttributes.position ?? 'right'
          this.closeOnEscape = server.panelAttributes.closeOnEscape ?? true
        }
      }),
    )
    this.listeners.push(
      Livewire.on('resizeSlideOverPanel', ({ maxWidthClass, id }) => {
        const targetId = id ?? this.activeComponent
        if (!targetId || !maxWidthClass) return
        if (this.resizedComponents[targetId] === maxWidthClass) return
        requestAnimationFrame(() => {
          this.resizedComponents = { ...this.resizedComponents, [targetId]: maxWidthClass }
          if (targetId === this.activeComponent) {
            this.panelWidth = maxWidthClass
          }
        })
      }),
    )
    this.listeners.push(
      Livewire.on('destroyComponent', ({ id }) => {
        delete this.resizedComponents[id]
      }),
    )
  },
  exposeGlobal() {
    const self = this
    window.$slideOver = {
      open(component, args = {}, attrs = {}) {
        const id = self.deriveId(component, args)
        self.openOptimistically(component, args, attrs, id)
        return id
      },
      close(force = false) { self.closePanel(force) },
      closeAll() { self.closePanel(true) },
    }
  },
  cleanup() {
    this.listeners.forEach((listener) => listener())
    Object.keys(this.optimisticTimeouts).forEach((tid) => this.clearOptimisticTimeout(tid))
    this.cancelCloseCleanup()
    if (window.$slideOver) delete window.$slideOver
  },
}

// === Stack ===

function createStackSlideOver() {
  return {
    ...slideOverCommon,
    stacked: true,
    inSwitch: false,
    isComponentVisible(id) {
      return id === this.activeComponent || this.componentHistory.includes(id)
    },
    getStackIndex(id) {
      if (id === this.activeComponent) return 0
      const historyIndex = this.componentHistory.indexOf(id)
      if (historyIndex === -1) return -1
      return this.componentHistory.length - historyIndex
    },
    getStackStyle(id) {
      const index = this.getStackIndex(id)
      if (index <= 0) return {}
      const position = this.getComponentPanelAttribute(id, 'position') ?? 'right'
      const dx = position === 'left' ? 1 : -1
      const offset = window.innerWidth < 640 ? 0.5 : 2
      return {
        transform: 'scale(' + (1 - 0.05 * index) + ') translateX(' + (offset * dx * index) + 'rem)',
        opacity: index <= 2 ? 1 : 0,
      }
    },
    setActivePanelComponent(id, skip = false) {
      this.setShowPropertyTo(true)
      if (this.activeComponent === id) return
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
        focusableTimeout = 600
        this.inSwitch = true
        setTimeout(() => {
          this.activeComponent = id
          this.showActiveComponent = true
          this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass')
          this.panelPosition = this.getActiveComponentPanelAttribute('position') ?? 'right'
          this.closeOnEscape = this.getActiveComponentPanelAttribute('closeOnEscape') ?? true
          setTimeout(() => { this.inSwitch = false }, 550)
        }, 0)
      }
      this.$nextTick(() => {
        const focusable = this.$refs[id]?.querySelector('[autofocus]')
        if (focusable) setTimeout(() => focusable.focus(), focusableTimeout)
      })
    },
    setShowPropertyTo(open) {
      this.open = open
      if (open) {
        this.cancelCloseCleanup()
        document.body.classList.add('overflow-y-hidden')
      } else {
        document.body.classList.remove('overflow-y-hidden')
        this.cancelCloseCleanup()
        this._closeCleanupTimeout = setTimeout(() => {
          this._closeCleanupTimeout = null
          this.activeComponent = false
          this.componentHistory = []
          Object.keys(this.optimisticTimeouts).forEach((tid) => this.clearOptimisticTimeout(tid))
          this.optimisticComponents = {}
          this.resizedComponents = {}
          this.$wire.resetState()
        }, 300)
      }
    },
    init() {
      this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass')
      this.panelPosition = this.getActiveComponentPanelAttribute('position') ?? 'right'
      this.setupListeners()
      this.exposeGlobal()
    },
    destroy() { this.cleanup() },
  }
}

// === Dialog ===

function createDialogSlideOver() {
  return {
    ...slideOverCommon,
    stacked: false,

    isComponentVisible(id) {
      return id === this.activeComponent
    },

    setActivePanelComponent(id, skip = false) {
      if (this.activeComponent === id) {
        if (this.open !== true) this.setShowPropertyTo(true)
        return
      }
      if (this.activeComponent !== false && skip === false) {
        this.componentHistory.push(this.activeComponent)
      }
      this.activeComponent = id
      this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass')
      this.panelPosition = this.getActiveComponentPanelAttribute('position') ?? 'right'
      this.closeOnEscape = this.getActiveComponentPanelAttribute('closeOnEscape') ?? true
      this.setShowPropertyTo(true)

      this.$nextTick(() => {
        const focusable = this.$refs[id]?.querySelector('[autofocus]')
        if (focusable) setTimeout(() => focusable.focus(), 100)
      })
    },

    setShowPropertyTo(open) {
      this.open = open
      const dialog = this.$refs.dialog
      if (open) {
        this.cancelCloseCleanup()
        if (dialog && !dialog.open && typeof dialog.showModal === 'function') {
          dialog.showModal()
        }
        document.body.classList.add('overflow-y-hidden')
      } else {
        document.body.classList.remove('overflow-y-hidden')
        if (dialog && dialog.open && typeof dialog.close === 'function') {
          dialog.close()
        }
        this.cancelCloseCleanup()
        this._closeCleanupTimeout = setTimeout(() => {
          this._closeCleanupTimeout = null
          this.activeComponent = false
          this.componentHistory = []
          Object.keys(this.optimisticTimeouts).forEach((tid) => this.clearOptimisticTimeout(tid))
          this.optimisticComponents = {}
          this.resizedComponents = {}
          this.$wire.resetState()
        }, 350)
      }
    },

    init() {
      this.panelWidth = this.getActiveComponentPanelAttribute('maxWidthClass')
      this.panelPosition = this.getActiveComponentPanelAttribute('position') ?? 'right'
      this.setupListeners()
      this.exposeGlobal()
    },
    destroy() { this.cleanup() },
  }
}

// === Entry ===

window.SlideOver = function (mode) {
  return mode === 'dialog' ? createDialogSlideOver() : createStackSlideOver()
}
