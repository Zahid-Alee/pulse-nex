import HeadingSmall from '@/components/heading-small';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import type { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Plan',
        href: '/settings/user-plan',
    },
];

export default function UserPlan() {
    const { userPlan, plans } = usePage().props as {
        userPlan: string;
        plans: {
            name: string;
            slug: string;
            info: string;
        }[];
    };

    const currentPlan = plans?.find((p) => p.slug === userPlan);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="User Plan" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall 
                        title="User Plan" 
                        description="Manage your account's subscription plan" 
                    />

                    <div className="rounded-lg border bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Current Plan: {currentPlan?.name}
                        </h2>
                        <p className="text-sm text-gray-600 dark:text-gray-400">
                            {currentPlan?.info}
                        </p>
                    </div>

                    <div className="mt-4">
                        <a 
                            href="/pricing" 
                            className="inline-block rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                        >
                            Upgrade Plan
                        </a>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
