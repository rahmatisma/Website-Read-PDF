import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { type ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
    enableSticky?: boolean; // Prop baru untuk sticky support
}

export default ({ children, breadcrumbs, enableSticky = false, ...props }: AppLayoutProps) => (
    <AppLayoutTemplate breadcrumbs={breadcrumbs} enableSticky={enableSticky} {...props}>
        {children}
    </AppLayoutTemplate>
);