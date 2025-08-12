// resources/js/Pages/dashboard/index.tsx
import WebsiteStatsChart from '@/components/websites/WebsiteStatsChart';
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';

export default function Dashboard() {
    const { props } = usePage<{ stats: any; websites: any }>();
    const { stats, websites } = props;

    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/dashboard' }]}>
            <Head title="Dashboard" />

            <div className="space-y-6 p-4">
                <h1 className="text-2xl font-bold">Dashboard Overview</h1>

                <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                    {/* Overall Uptime */}
                    {/* Total Websites */}
                    <div className="rounded-lg border border-green-200 bg-gradient-to-r from-green-50 to-green-100 p-6 dark:border-green-700 dark:from-green-900 dark:to-green-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-green-500">
                                    <svg className="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 4a2 2 0 012-2h12a2 2 0 012 2v3H2V4zM2 9h16v3H2V9zm0 5h16v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-green-900 dark:text-green-100">{stats.total_websites}</div>
                                <div className="text-sm font-medium text-green-700 dark:text-green-300">Total Websites</div>
                            </div>
                        </div>
                    </div>

                    {/* Uptime Percentage */}
                    <div className="rounded-lg border border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100 p-6 dark:border-blue-700 dark:from-blue-900 dark:to-blue-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
                                    <svg className="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 17l6-6 4 4 7-7v9a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <path d="M14 7h2V5h-2v2z" />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-blue-900 dark:text-blue-100">{stats.uptime_percentage ?? 'N/A'}%</div>
                                <div className="text-sm font-medium text-blue-700 dark:text-blue-300">Uptime Percentage</div>
                            </div>
                        </div>
                    </div>

                    {/* Average Response Time */}
                    <div className="rounded-lg border border-purple-200 bg-gradient-to-r from-purple-50 to-purple-100 p-6 dark:border-purple-700 dark:from-purple-900 dark:to-purple-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-purple-500">
                                    <svg className="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fillRule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-8.75V5a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                    {stats.average_response_time ?? 'N/A'} ms
                                </div>
                                <div className="text-sm font-medium text-purple-700 dark:text-purple-300">Average Response Time</div>
                            </div>
                        </div>
                    </div>
                </div>

                <WebsiteStatsChart hideCards data={stats.hourly_data} uptimePercentage={stats.uptime_percentage ?? 0} />
            </div>
        </AppLayout>
    );
}
