import { ref } from 'vue'
import { isNavLinkActive, navLinkProps } from './utils'

export default function useVerticalNavMenuLink(item) {
    const isActive = ref(false)

    const linkProps = navLinkProps(item)

    const updateIsActive = () => {
        isActive.value = isNavLinkActive(item)
    }

    return {
        isActive,
        linkProps,
        updateIsActive,
    }
}
