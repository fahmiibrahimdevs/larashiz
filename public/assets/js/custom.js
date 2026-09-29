/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

(function () {
  var MOBILE_QUERY = "(max-width: 767.98px)";
  var DISMISS_MIN = 80;      // px minimum drag distance
  var DISMISS_RATIO = 0.25;  // or 25% of the sheet height, whichever is greater

  var drag = null;

  function isMobile() {
    return window.matchMedia(MOBILE_QUERY).matches;
  }

  function resetDialog(dialog) {
    if (!dialog) return;
    dialog.style.transition = "";
    dialog.style.translate = "";
  }

  function onPointerDown(e) {
    if (!isMobile()) return;
    if (e.button !== undefined && e.button !== 0) return;
    if (!e.target.closest) return;

    var header = e.target.closest(".modal-header");
    if (!header) return;

    var modal = header.closest(".modal");
    if (!modal || !modal.classList.contains("show")) return;

    // Ignore interactive controls inside the header (e.g. the close button).
    if (e.target.closest("button, a, input, select, textarea, label")) return;

    var dialog = modal.querySelector(".modal-dialog");
    if (!dialog) return;

    drag = { modal: modal, dialog: dialog, startY: e.clientY, dy: 0, id: e.pointerId };
    dialog.style.transition = "none";
    document.documentElement.classList.add("modal-sheet-dragging");
  }

  function onPointerMove(e) {
    if (!drag || e.pointerId !== drag.id) return;

    var dy = e.clientY - drag.startY;
    if (dy < 0) dy = dy * 0.2; // rubber-band when dragged upwards
    drag.dy = dy;
    drag.dialog.style.translate = "0px " + dy + "px";
    if (e.cancelable) e.preventDefault();
  }

  function onPointerUp(e) {
    if (!drag || e.pointerId !== drag.id) return;

    var current = drag;
    drag = null;
    document.documentElement.classList.remove("modal-sheet-dragging");

    var height = current.dialog.offsetHeight || 1;
    var threshold = Math.max(DISMISS_MIN, height * DISMISS_RATIO);

    if (current.dy > threshold) {
      dismiss(current.modal, current.dialog);
    } else {
      resetDialog(current.dialog);
    }
  }

  function dismiss(modal, dialog) {
    var height = dialog.offsetHeight || 0;
    var done = false;

    function finish() {
      if (done) return;
      done = true;
      resetDialog(dialog);
      if (window.jQuery) {
        window.jQuery(modal).modal("hide");
      } else {
        modal.classList.remove("show");
        modal.style.display = "none";
      }
    }

    dialog.style.transition = "translate 300ms ease-out";
    dialog.style.translate = "0px " + height + "px";
    dialog.addEventListener("transitionend", finish, { once: true });
    setTimeout(finish, 340);
  }

  document.addEventListener("pointerdown", onPointerDown);
  document.addEventListener("pointermove", onPointerMove);
  document.addEventListener("pointerup", onPointerUp);
  document.addEventListener("pointercancel", onPointerUp);
})();
