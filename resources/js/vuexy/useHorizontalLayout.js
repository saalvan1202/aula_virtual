import { computed } from 'vue'
export default function useHorizontalLayout(navbarMenuType, footerType, isVerticalMenuActive,currentBreakpoint) {
    const layoutClasses = computed(() => {
        const classes = []

        if (currentBreakpoint.value === 'xl') {
            classes.push('horizontal-menu')
        } else {
            classes.push('vertical-overlay-menu')
            classes.push(isVerticalMenuActive.value ? 'menu-open' : 'menu-hide')
        }

        // Navbar
        classes.push(`navbar-${navbarMenuType.value}`)

        // Footer
        if (footerType.value === 'sticky') classes.push('footer-fixed')
        if (footerType.value === 'static') classes.push('footer-static')
        if (footerType.value === 'hidden') classes.push('footer-hidden')

        return classes
    });
    const navbarMenuTypeClass = computed(() => {
        if (navbarMenuType.value === 'sticky') return 'fixed-top'
        if (navbarMenuType.value === 'static') return ''
        if (navbarMenuType.value === 'hidden') return 'd-none'
        return 'floating-nav'
    });

    return {
        layoutClasses,
        navbarMenuTypeClass
    }
}
