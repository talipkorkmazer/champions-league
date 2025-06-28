<script setup lang="ts">
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import NavMain from '@/components/NavMain.vue';
import NavFooter from '@/components/NavFooter.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { Home, Info } from 'lucide-vue-next';
import { type NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: Home,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'About',
        href: '/',
        icon: Info,
    },
];
</script>

<template>
    <Sidebar class="border-r bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <SidebarHeader>
            <AppSidebarHeader />
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>
        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
</template>
