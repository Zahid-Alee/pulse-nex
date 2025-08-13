import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Cog, LayoutGrid, MessageSquare, PanelTop, UserRoundPen } from 'lucide-react';
import AppLogo from './app-logo';
import { ToastProviderWrapper } from './ui/use-toast';

export function AppSidebar() {
    const { auth } = usePage<SharedData>().props;

    const mainNavItems: NavItem[] = [];

    if (auth.user?.is_admin == 1) {
        mainNavItems.push({
            title: 'Admin Dashboard',
            href: '/admin/dashboard',
            icon: PanelTop,
        });
    } else {
        mainNavItems.push({
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        });
    }

    mainNavItems.push({
        title: 'Websites',
        href: '/websites',
        icon: PanelTop,
    });

    if (auth.user?.is_admin == 1) {
        mainNavItems.push({
            title: 'Users',
            href: '/admin/users',
            icon: UserRoundPen,
        });
    }
    if (auth.user?.is_admin == 1) {
        mainNavItems.push({
            title: 'User Queries',
            href: '/admin/contacts',
            icon: MessageSquare,
        });
    }

    mainNavItems.push({
        title: 'Settings',
        href: '/settings/profile',
        icon: Cog,
    });

    return (
        <ToastProviderWrapper>
            <Sidebar collapsible="icon" variant="inset">
                <SidebarHeader>
                    <SidebarMenu>
                        <SidebarMenuItem>
                            <SidebarMenuButton size="lg" asChild>
                                <Link href="/dashboard" prefetch>
                                    <AppLogo />
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarHeader>

                <SidebarContent>
                    <NavMain items={mainNavItems} />
                </SidebarContent>

                <SidebarFooter>
                    <NavUser />
                </SidebarFooter>
            </Sidebar>
        </ToastProviderWrapper>
    );
}
