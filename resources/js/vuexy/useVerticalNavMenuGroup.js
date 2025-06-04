import { ref, watch, inject } from 'vue'
import {isNavGroupActive} from "./utils";
export default function useVerticalNavMenuGroup(item) {
    const openGroups = inject('openGroups')
    const isOpen = ref(false)
    const isActive = ref(false)
    const isVerticalMenuCollapsed=ref(false)
    const updateGroupOpen = val => {
        // eslint-disable-next-line no-use-before-define
        isOpen.value = val
    }
    const doesHaveChild = id => item.children.some(child => child.id == id)
    const updateIsActive = () => {
        isActive.value = isNavGroupActive(item.children)
    }

    watch(isOpen, val => {
        // if group is opened push it to the array

        if (val) openGroups.value.push(item.id)
    })
    // Collapse other groups if one group is opened
    watch(openGroups, currentOpenGroups => {
        const clickedGroup = currentOpenGroups[currentOpenGroups.length - 1]


        // If current group is not clicked group or current group is not active => Proceed with closing it
        // eslint-disable-next-line no-use-before-define
        if (clickedGroup != item.id && !isActive.value) {
            // If clicked group is not child of current group
            // eslint-disable-next-line no-use-before-define
            if (!doesHaveChild(clickedGroup)) isOpen.value = false
        }
    }, { deep: true })
    watch(isActive, val => {
        //console.log('active watch',val)
        if(val){
            if (!isVerticalMenuCollapsed.value) isOpen.value = val
        }else {
            isOpen.value = val
        }
    })


    return {
        isOpen,
        isActive,
        updateGroupOpen,
        updateIsActive,
        openGroups
    }
}
