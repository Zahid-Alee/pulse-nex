// components/admin/AdminStatsCharts.tsx
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Legend,
    Line,
    LineChart,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

const AdminStatsCharts = ({ data }) => {
    const websiteStatusData = [
        { name: 'Up', value: data.websites.website_status_distribution.up, color: '#10B981' },
        { name: 'Down', value: data.websites.website_status_distribution.down, color: '#EF4444' },
        { name: 'Unknown', value: data.websites.website_status_distribution.unknown, color: '#6B7280' },
    ];

    const dailyRegistrations = data.charts.user_registrations.map((item) => ({
        ...item,
        date: new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
    }));

    const dailyWebsites = data.charts.website_additions.map((item) => ({
        ...item,
        date: new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
    }));

    const dailyUptime = data.charts.daily_uptime.map((item) => ({
        ...item,
        date: new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
    }));

    const responseTimeTrend = data.charts.response_time_trend.map((item) => ({
        ...item,
        date: new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
    }));

    return (
        <div className="space-y-6">
            {/* Charts Grid */}
            <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {/* Website Status Distribution */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Website Status Distribution</h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <PieChart>
                            <Pie
                                data={websiteStatusData}
                                cx="50%"
                                cy="50%"
                                labelLine={false}
                                label={({ name, value }) => `${name}: ${value}`}
                                outerRadius={80}
                                fill="#8884d8"
                                dataKey="value"
                            >
                                {websiteStatusData.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={entry.color} />
                                ))}
                            </Pie>
                            <Tooltip />
                            <Legend verticalAlign="bottom" height={36} iconType="circle" />
                        </PieChart>
                    </ResponsiveContainer>
                </div>

                {/* Daily User Registrations */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">User Registrations</h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <AreaChart data={dailyRegistrations}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                            <XAxis dataKey="date" stroke="#6b7280" fontSize={12} />
                            <YAxis stroke="#6b7280" fontSize={12} />
                            <Tooltip />
                            <Area type="monotone" dataKey="count" stroke="#3B82F6" fill="#3B82F6" fillOpacity={0.6} />
                        </AreaChart>
                    </ResponsiveContainer>
                </div>

                {/* Daily Website Additions */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Website Additions</h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <BarChart data={dailyWebsites}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                            <XAxis dataKey="date" stroke="#6b7280" fontSize={12} />
                            <YAxis stroke="#6b7280" fontSize={12} />
                            <Tooltip />
                            <Bar dataKey="count" fill="#10B981" radius={[4, 4, 0, 0]} />
                        </BarChart>
                    </ResponsiveContainer>
                </div>

                {/* Overall Uptime Trend */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Overall Uptime Trend</h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <LineChart data={dailyUptime}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                            <XAxis dataKey="date" stroke="#6b7280" fontSize={12} />
                            <YAxis domain={[95, 100]} stroke="#6b7280" fontSize={12} />
                            <Tooltip />
                            <Line
                                type="monotone"
                                dataKey="uptime_percentage"
                                stroke="#10B981"
                                strokeWidth={3}
                                dot={{ fill: '#10B981', strokeWidth: 2, r: 4 }}
                            />
                        </LineChart>
                    </ResponsiveContainer>
                </div>
            </div>

            {/* Full Width Charts */}
            <div className="space-y-6">
                {/* Response Time Trend */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Average Response Time Trend</h3>
                    <ResponsiveContainer width="100%" height={400}>
                        <AreaChart data={responseTimeTrend}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                            <XAxis dataKey="date" stroke="#6b7280" fontSize={12} />
                            <YAxis stroke="#6b7280" fontSize={12} />
                            <Tooltip formatter={(value) => [`${value} ms`, 'Response Time']} labelFormatter={(label) => `Date: ${label}`} />
                            <Area type="monotone" dataKey="avg_response_time" stroke="#F59E0B" fill="#F59E0B" fillOpacity={0.3} />
                        </AreaChart>
                    </ResponsiveContainer>
                </div>

                {/* Top Users by Websites */}
            

                {/* Slowest Websites */}
              
            </div>

            {/* System Health Summary */}
            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">System Health Summary</h3>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div className="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700 dark:bg-green-900/20">
                        <div className="flex items-center">
                            <div className="mr-3 h-3 w-3 rounded-full bg-green-500"></div>
                            <div>
                                <div className="font-medium text-green-900 dark:text-green-100">
                                    System Uptime: {data.performance.overall_uptime}%
                                </div>
                                <div className="text-sm text-green-700 dark:text-green-300">
                                    {data.performance.total_checks_performed} total checks performed
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900/20">
                        <div className="flex items-center">
                            <div className="mr-3 h-3 w-3 rounded-full bg-blue-500"></div>
                            <div>
                                <div className="font-medium text-blue-900 dark:text-blue-100">Active Users: {data.overview.active_users}</div>
                                <div className="text-sm text-blue-700 dark:text-blue-300">{data.overview.total_users} total registered users</div>
                            </div>
                        </div>
                    </div>

                    <div className="rounded-lg border border-purple-200 bg-purple-50 p-4 dark:border-purple-700 dark:bg-purple-900/20">
                        <div className="flex items-center">
                            <div className="mr-3 h-3 w-3 rounded-full bg-purple-500"></div>
                            <div>
                                <div className="font-medium text-purple-900 dark:text-purple-100">
                                    Avg Response: {data.performance.average_response_time}ms
                                </div>
                                <div className="text-sm text-purple-700 dark:text-purple-300">{data.overview.total_websites} websites monitored</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default AdminStatsCharts;
