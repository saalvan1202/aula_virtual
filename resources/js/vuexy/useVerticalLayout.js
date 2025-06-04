import {ref,computed,watch} from "vue";

export default function useVerticalLayout(navbarType, footerType) {
    const isVerticalMenuActive = ref(true)
    const currentBreakpoint = ref('xl')
    const toggleVerticalMenuActive = () => {
        isVerticalMenuActive.value = !isVerticalMenuActive.value
    }
    const layoutClasses = computed(() => {
        const classes = []

        if (currentBreakpoint.value === 'xl') {
          classes.push('vertical-menu-modern')
          classes.push('menu-expanded')
          //classes.push(isVerticalMenuCollapsed.value ? 'menu-collapsed' : 'menu-expanded')
        } else {
          classes.push('vertical-overlay-menu')
          classes.push(isVerticalMenuActive.value ? 'menu-open' : 'menu-hide')
        }

        // Navbar
        classes.push(`navbar-${navbarType.value}`)

        // Footer
        if (footerType.value === 'sticky') classes.push('footer-fixed')
        if (footerType.value === 'static') classes.push('footer-static')
        if (footerType.value === 'hidden') classes.push('footer-hidden')

        return classes
    })

    const resizeHandler = () => {
        if (window.innerWidth >= 1200) currentBreakpoint.value = 'xl'
        else if (window.innerWidth >= 992) currentBreakpoint.value = 'lg'
        else if (window.innerWidth >= 768) currentBreakpoint.value = 'md'
        else if (window.innerWidth >= 576) currentBreakpoint.value = 'sm'
        else currentBreakpoint.value = 'xs'
    }
    const overlayClasses = computed(() => {
        if (currentBreakpoint.value !== 'xl' && isVerticalMenuActive.value) return 'show'
        return null
    })
    watch(currentBreakpoint, val => {
        isVerticalMenuActive.value = val === 'xl'
    })
    return{
        isVerticalMenuActive,
        layoutClasses,
        overlayClasses,
        currentBreakpoint,
        toggleVerticalMenuActive,
        resizeHandler
    }
}
