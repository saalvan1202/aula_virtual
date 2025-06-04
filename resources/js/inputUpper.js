export default {
    bind: function (el, _, vnode) {

        /*el.transformUpper = function () {
          const start = el.selectionStart
          const end=el.selectionEnd
          el.value = el.value.toLocaleUpperCase()
          el.setSelectionRange(start, end)
          //vnode.context.$emit('input',el.value.toLocaleUpperCase());
          vnode.elm.dispatchEvent(new CustomEvent('input'));
        }
        // add event listener
        el.addEventListener('input', function () {
          el.transformUpper()
        })*/
    },
    unbind: function (el) {
        el.removeEventListener('input', el.transformUpper)
    },
    update: function (el, _, vnode) {
        const start = el.selectionStart
        const end = el.selectionEnd
        el.value = el.value.toLocaleUpperCase()
        el.setSelectionRange(start, end)
        //vnode.context.$emit('input',el.value.toLocaleUpperCase());
        vnode.elm.dispatchEvent(new CustomEvent('input'));
    }
}