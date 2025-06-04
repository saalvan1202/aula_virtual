import {computed} from "vue";
import {currentUrl} from '../helpers.js'
import {usePage} from "@inertiajs/vue2";

export const resolveVerticalNavMenuItemComponent = item => {
    if (item.header) return 'VerticalNavMenuHeader'
    if (item.children!=undefined && item.children.length>0) return 'VerticalNavMenuGroup'
    return 'VerticalNavMenuLink'
}
export const resolveHorizontalNavMenuItemComponent = item => {
    if (item.header) return 'horizontal-nav-menu-header'
    if (item.children!=undefined && item.children.length>0) return 'horizontal-nav-menu-group'
    return 'horizontal-nav-menu-link'
}

export const isNavGroupActive=children=>{
    //console.log('children',children)
    const matchedRoutes=currentUrl()
    return children.some(child => {
        if (child.children!=undefined && child.children.length>0) {
            return isNavGroupActive(child.children)
        }
        return isNavLinkActive(child, matchedRoutes)
    })

}
export const isNavLinkActive = link => {
    const matchedRoutes=currentUrl()
    const resolveRoutedName=link.route
    return matchedRoutes===resolveRoutedName || routeMatchDependency(matchedRoutes,resolveRoutedName);
}
export const navLinkProps = item => computed(() => {
    const props = {}
    props.to={ name: item.route }
    if (!props.target) props.target = item.target || null

    return props
})
export const routeMatchDependency=(current,link)=>{
    const page=usePage();
    const dependencia=page.props.menu_dependencia||[]
    const found=dependencia.filter((el)=>{
        return el.url===current
    });
    if(found.length){
        return found[0].menu===link;
    }
    return false;
}
