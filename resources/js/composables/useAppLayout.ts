import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { LayoutGrid, House, Settings, LogOut, Users, UserCog, DollarSign } from 'lucide-vue-next';
import { MenuItem } from '@/types';

export function useAppLayout() {
    const page = usePage();
    const isAdmin = page.props.auth.user?.is_admin == true ? true : false;
    const currentRoute = computed(() => {
        // Access page.url to trigger re-computation on navigation.
        /* eslint-disable @typescript-eslint/no-unused-vars */
        const url = page.url;
        /* eslint-enable @typescript-eslint/no-unused-vars */
        return route().current();
    });

    // Menu items
    const menuItems = computed<MenuItem[]>(() => [
        {
            label: 'Página Inicial',
            lucideIcon: House,
            route: route('home'),
            active: currentRoute.value == 'home',
        },
        {
            label: 'Dashboard',
            lucideIcon: LayoutGrid,
            route: route('dashboard'),
            active: currentRoute.value == 'dashboard',
        },
        {
            label: 'Membros',
            lucideIcon: Users,
            route: route('members.index'),
            active: currentRoute.value == 'members.index',
        },
        {
            label: 'Mensalidades',
            lucideIcon: DollarSign,
            route: route('subscriptions.index'),
            active: currentRoute.value == 'subscriptions.index',
        },
        {
            label: 'Usuários do Sistema',
            lucideIcon: UserCog,
            route: route('users.index'),
            active: currentRoute.value == 'users.index',
            visible: isAdmin,
        },
    ]);

    // User menu and logout functionality.
    const logoutForm = useForm({});
    const logout = () => {
        logoutForm.post(route('logout'));
    };
    const userMenuItems: MenuItem[] = [
        {
            label: 'Configurações',
            route: route('profile.edit'),
            lucideIcon: Settings,
        },
        {
            separator: true
        },
        {
            label: 'Sair',
            lucideIcon: LogOut,
            command: () => logout(),
        },
    ];

    // Mobile menu
    const mobileMenuOpen = ref(false);
    if (typeof window !== 'undefined') {
        const windowWidth = ref(window.innerWidth);
        const updateWidth = () => {
            windowWidth.value = window.innerWidth;
        };
        onMounted(() => {
            window.addEventListener('resize', updateWidth);
        });
        onUnmounted(() => {
            window.removeEventListener('resize', updateWidth);
        });
        watchEffect(() => {
            if (windowWidth.value > 1024) {
                mobileMenuOpen.value = false;
            }
        });
    }

    return {
        currentRoute,
        menuItems,
        userMenuItems,
        mobileMenuOpen,
        logout,
    };
}
