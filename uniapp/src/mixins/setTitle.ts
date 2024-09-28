import { useNavigationBarTitleStore } from '@/stores/navigationBarTitle'

export default {
    onShow() {
        const navigationBarTitleStore = useNavigationBarTitleStore()
        navigationBarTitleStore.setTitle()
    }
}
