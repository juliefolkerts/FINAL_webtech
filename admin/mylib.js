// MiniLib: a tiny reusable library for show/hide and modal popups.
// Implemented as a class so methods are namespaced: MiniLib.show(...)
// Comments explain purpose of each section and line.

class MiniLib {
  constructor() {
    // Keep reference to the currently open modal (if any)
    this._currentModal = null;
    // Save the element that had focus before modal opened
    this._previousActive = null;

    // Bind methods so event handlers can remove them later
    this._onKeyDown = this._onKeyDown.bind(this);
    this._onDocumentClick = this._onDocumentClick.bind(this);
  }

  /* ---------------------------
     Show / hide utilities
     Accept either a selector string or a DOM element.
     These functions are tiny wrappers that:
      - set aria-hidden for accessibility
      - toggle inline style 'display' if no other class controls visibility
  --------------------------- */

  show(target) {
    const el = this._getElement(target);
    if (!el) return;
    el.style.display = el.__mylib_savedDisplay || ''; // restore saved display or default
    el.removeAttribute('aria-hidden');
    el.classList.remove('mylib-hidden');
  }

  hide(target) {
    const el = this._getElement(target);
    if (!el) return;
    // Save current computed display so show() can restore it
    const cs = getComputedStyle(el).display;
    if (cs !== 'none') el.__mylib_savedDisplay = cs;
    el.style.display = 'none';
    el.setAttribute('aria-hidden', 'true');
    el.classList.add('mylib-hidden');
  }

  toggle(target) {
    const el = this._getElement(target);
    if (!el) return;
    const cs = getComputedStyle(el).display;
    if (cs === 'none' || el.classList.contains('mylib-hidden')) {
      this.show(el);
    } else {
      this.hide(el);
    }
  }

  /* Helper: accept selector or element */
  _getElement(target) {
    if (!target) return null;
    if (typeof target === 'string') {
      return document.querySelector(target);
    }
    if (target instanceof Element) return target;
    return null;
  }

  /* ---------------------------
     Modal implementation
     Usage:
       const lib = new MiniLib();
       lib.openModal({ title: 'Hello', content: '<p>Hi</p>' });
     Or pass a DOM node as content:
       lib.openModal({ title: 'Form', contentNode: document.querySelector('#my-form') });
  --------------------------- */

  openModal({ title = '', content = '', contentNode = null, closable = true } = {}) {
    // If a modal is already open, close it first
    if (this._currentModal) this.closeModal();

    // Save currently focused element to restore later
    this._previousActive = document.activeElement;

    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'mylib-modal-overlay';
    overlay.tabIndex = -1; // make focusable so we can trap focus
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');

    // Create dialog
    const dialog = document.createElement('div');
    dialog.className = 'mylib-modal';
    dialog.setAttribute('role', 'document');

    // Header
    const header = document.createElement('div');
    header.className = 'mylib-modal-header';
    const h = document.createElement('h2');
    h.className = 'mylib-modal-title';
    h.textContent = title;
    header.appendChild(h);

    // Close button
    if (closable) {
      const closeBtn = document.createElement('button');
      closeBtn.className = 'mylib-modal-close';
      closeBtn.setAttribute('aria-label', 'Close dialog');
      closeBtn.innerHTML = '&times;';
      closeBtn.addEventListener('click', () => this.closeModal());
      header.appendChild(closeBtn);
    }

    // Content area
    const body = document.createElement('div');
    body.className = 'mylib-modal-body';
    if (contentNode instanceof Element) {
      // If user passed an existing node, clone it to avoid moving original
      body.appendChild(contentNode.cloneNode(true));
    } else {
      // allow string HTML or plain text
      body.innerHTML = content;
    }

    // Assemble
    dialog.appendChild(header);
    dialog.appendChild(body);
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);

    // Save reference
    this._currentModal = overlay;

    // Add event handlers
    document.addEventListener('keydown', this._onKeyDown);
    setTimeout(() => {
      // use setTimeout so focus happens after element is in DOM
      overlay.focus();
      // focus first focusable element inside modal (or close button)
      this._focusFirstElement(dialog);
    }, 0);
    // Close on click outside dialog
    overlay.addEventListener('click', this._onDocumentClick);

    // Prevent background from scrolling while modal is open
    document.documentElement.classList.add('mylib-modal-open');

    // Return overlay element in case caller wants to inspect
    return overlay;
  }

  closeModal() {
    if (!this._currentModal) return;
    // Remove event listeners
    document.removeEventListener('keydown', this._onKeyDown);
    this._currentModal.removeEventListener('click', this._onDocumentClick);

    // Remove modal from DOM
    if (this._currentModal.parentNode) {
      this._currentModal.parentNode.removeChild(this._currentModal);
    }
    this._currentModal = null;

    // Restore focus
    if (this._previousActive && typeof this._previousActive.focus === 'function') {
      this._previousActive.focus();
    }
    this._previousActive = null;

    // Re-enable background scrolling
    document.documentElement.classList.remove('mylib-modal-open');
  }

  /* ---------------------------
     Internal helpers for modal behavior
  --------------------------- */

  // Handle Escape key to close modal and Tab key to trap focus
  _onKeyDown(e) {
    if (!this._currentModal) return;
    if (e.key === 'Escape') {
      e.preventDefault();
      this.closeModal();
      return;
    }
    if (e.key === 'Tab') {
      this._trapFocus(e);
      return;
    }
  }

  // Clicks on overlay but outside the modal content should close it
  _onDocumentClick(e) {
    if (!this._currentModal) return;
    const dialog = this._currentModal.querySelector('.mylib-modal');
    if (!dialog) return;
    // If click happened on overlay (not inside dialog), close
    if (!dialog.contains(e.target)) {
      this.closeModal();
    }
  }

  // Focus the first focusable element in the modal (or the close button / overlay)
  _focusFirstElement(container) {
    const focusables = this._getFocusable(container);
    if (focusables.length) {
      focusables[0].focus();
    } else {
      // fallback to overlay
      this._currentModal && this._currentModal.focus();
    }
  }

  // Trap focus within the modal (simple implementation)
  _trapFocus(event) {
    const dialog = this._currentModal && this._currentModal.querySelector('.mylib-modal');
    if (!dialog) return;
    const focusables = this._getFocusable(dialog);
    if (!focusables.length) {
      event.preventDefault();
      return;
    }
    const first = focusables[0];
    const last = focusables[focusables.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      // shift+tab on first -> go to last
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      // tab on last -> go to first
      event.preventDefault();
      first.focus();
    }
  }

  // Return list of focusable elements inside container
  _getFocusable(container) {
    if (!container) return [];
    const selector = [
      'a[href]',
      'area[href]',
      'input:not([disabled])',
      'select:not([disabled])',
      'textarea:not([disabled])',
      'button:not([disabled])',
      'iframe',
      'object',
      'embed',
      '[tabindex]:not([tabindex="-1"])',
      '[contenteditable]'
    ].join(',');
    return Array.from(container.querySelectorAll(selector)).filter(el => el.offsetWidth > 0 || el.offsetHeight > 0 || el === document.activeElement);
  }
}

// Export a default instance so users can just do: const lib = new MiniLib();
// If you prefer a singleton across files: window.MiniLib = new MiniLib();
window.MiniLib = new MiniLib();
