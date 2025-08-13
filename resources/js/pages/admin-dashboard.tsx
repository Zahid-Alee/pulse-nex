// resources/js/Pages/admin/dashboard.tsx
import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import AdminStatsCharts from './admin-stats';

export default function AdminDashboard() {
    const { props } = usePage<{ stats: any }>();
    const { stats } = props;

    return (
        <AppLayout breadcrumbs={[{ title: 'Admin Dashboard', href: '/admin/dashboard' }]}>
            <Head title="Admin Dashboard" />

            <div className="space-y-6 p-4">
                <h1 className="text-2xl font-bold">Admin Dashboard</h1>

                {/* Overview Stats */}
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    {/* Total Users */}
                    <div className="rounded-lg border border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100 p-6 dark:border-blue-700 dark:from-blue-900 dark:to-blue-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500">
                                    <svg className="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-blue-900 dark:text-blue-100">{stats.overview.total_users}</div>
                                <div className="text-sm font-medium text-blue-700 dark:text-blue-300">Total Users</div>
                                <div className="text-xs text-blue-600 dark:text-blue-400">{stats.overview.new_users_today} new today</div>
                            </div>
                        </div>
                    </div>

                    {/* Total Websites */}
                    <div className="rounded-lg border border-green-200 bg-gradient-to-r from-green-50 to-green-100 p-6 dark:border-green-700 dark:from-green-900 dark:to-green-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-green-500">
                                    <svg className="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-green-900 dark:text-green-100">{stats.overview.total_websites}</div>
                                <div className="text-sm font-medium text-green-700 dark:text-green-300">Total Websites</div>
                                <div className="text-xs text-green-600 dark:text-green-400">
                                    {stats.overview.websites_up} up, {stats.overview.websites_down} down
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Active Users */}
                    <div className="rounded-lg border border-purple-200 bg-gradient-to-r from-purple-50 to-purple-100 p-6 dark:border-purple-700 dark:from-purple-900 dark:to-purple-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-purple-500">
                                    <svg className="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fillRule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-purple-900 dark:text-purple-100">{stats.overview.active_users}</div>
                                <div className="text-sm font-medium text-purple-700 dark:text-purple-300">Active Users</div>
                                <div className="text-xs text-purple-600 dark:text-purple-400">Last 30 days</div>
                            </div>
                        </div>
                    </div>

                    {/* Overall Uptime */}
                    <div className="rounded-lg border border-orange-200 bg-gradient-to-r from-orange-50 to-orange-100 p-6 dark:border-orange-700 dark:from-orange-900 dark:to-orange-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500">
                                    <svg className="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fillRule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-orange-900 dark:text-orange-100">{stats.performance.overall_uptime}%</div>
                                <div className="text-sm font-medium text-orange-700 dark:text-orange-300">Overall Uptime</div>
                                <div className="text-xs text-orange-600 dark:text-orange-400">{stats.performance.average_response_time}ms avg</div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Recent Users & Top Websites */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* Recent Users */}
                    <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Users</h3>
                        <div className="space-y-3">
                            {stats.users.recent_users.slice(0, 5).map((user, index) => (
                                <div
                                    key={user.id}
                                    className="flex items-center justify-between rounded-lg border border-gray-100 p-3 dark:border-gray-700"
                                >
                                    <div className="flex items-center">
                                        <div className="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-medium text-white">
                                            {user.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <div className="font-medium text-gray-900 dark:text-gray-100">{user.name}</div>
                                            <div className="text-sm text-gray-500 dark:text-gray-400">{user.email}</div>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="text-sm font-medium text-gray-900 dark:text-gray-100">{user.websites_count} websites</div>
                                        <div className="text-xs text-gray-500 dark:text-gray-400">
                                            {new Date(user.created_at).toLocaleDateString()}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Top Websites */}
                    <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Top Websites</h3>
                        <div className="space-y-3">
                            {stats.websites.top_websites.slice(0, 5).map((website, index) => (
                                <div
                                    key={website.id}
                                    className="flex items-center justify-between rounded-lg border border-gray-100 p-3 dark:border-gray-700"
                                >
                                    <div className="flex items-center">
                                        <div className={`mr-3 h-3 w-3 rounded-full ${website.status === 'up' ? 'bg-green-500' : 'bg-red-500'}`}></div>
                                        <div>
                                            <div className="font-medium text-gray-900 dark:text-gray-100">{website.name}</div>
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                {website.url} • {website.user_name}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <div className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {website.uptime_percentage}% uptime
                                        </div>
                                        <div className="text-xs text-gray-500 dark:text-gray-400">{website.total_checks} checks</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Recent Incidents */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Incidents</h3>
                    <div className="space-y-3">
                        {stats.websites.recent_incidents.slice(0, 5).map((incident, index) => (
                            <div
                                key={index}
                                className="flex items-center justify-between rounded-lg border border-red-100 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20"
                            >
                                <div className="flex items-center">
                                    <div className="mr-3 h-3 w-3 rounded-full bg-red-500"></div>
                                    <div>
                                        <div className="font-medium text-gray-900 dark:text-gray-100">{incident.website_name}</div>
                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                            {incident.website_url} • {incident.user_name}
                                        </div>
                                        <div className="text-sm text-red-600 dark:text-red-400">{incident.error_message}</div>
                                    </div>
                                </div>
                                <div className="text-right">
                                    <div className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {incident.status_code && `HTTP ${incident.status_code}`}
                                    </div>
                                    <div className="text-xs text-gray-500 dark:text-gray-400">{new Date(incident.checked_at).toLocaleString()}</div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Charts */}
                <AdminStatsCharts data={stats} />
            </div>
        </AppLayout>
    );
}
