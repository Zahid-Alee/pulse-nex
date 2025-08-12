import { Bar, BarChart, CartesianGrid, Cell, Legend, Line, LineChart, Pie, PieChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

const WebsiteStatsChart = ({ data, uptimePercentage, hideCards = false }) => {
    const processedData =
        data?.map((item, index) => ({
            ...item,
            time: new Date(item.hour).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            }),
            fullTime: item.hour,
            downtime_percentage: 100 - item.uptime_percentage,
            index: index + 1,
        })) || [];

    const pieData = [
        { name: 'Uptime', value: uptimePercentage, color: '#10B981' },
        { name: 'Downtime', value: 100 - uptimePercentage, color: '#EF4444' },
    ];

    const CustomTooltip = ({ active, payload, label }) => {
        if (active && payload && payload.length) {
            return (
                <div className="rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{`Time: ${label}`}</p>
                    <p className="text-sm text-green-600 dark:text-green-400">{`Uptime: ${payload[0].value}%`}</p>
                    {payload[0].value < 100 && <p className="text-sm text-red-600 dark:text-red-400">{`Downtime: ${100 - payload[0].value}%`}</p>}
                </div>
            );
        }
        return null;
    };

    const PieTooltip = ({ active, payload }) => {
        if (active && payload && payload.length) {
            return (
                <div className="rounded-lg border border-gray-200 bg-white p-3 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{`${payload[0].name}: ${payload[0].value}%`}</p>
                </div>
            );
        }
        return null;
    };

    return (
        <div className="space-y-6">
            {/* Overview Cards */}
            {!hideCards && (
                <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                    {/* Overall Uptime */}
                    <div className="rounded-lg border border-green-200 bg-gradient-to-r from-green-50 to-green-100 p-6 dark:border-green-700 dark:from-green-900 dark:to-green-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-green-500">
                                    <svg className="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fillRule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-green-900 dark:text-green-100">{uptimePercentage}%</div>
                                <div className="text-sm font-medium text-green-700 dark:text-green-300">Overall Uptime</div>
                            </div>
                        </div>
                    </div>

                    {/* Data Points */}
                    <div className="rounded-lg border border-blue-200 bg-gradient-to-r from-blue-50 to-blue-100 p-6 dark:border-blue-700 dark:from-blue-900 dark:to-blue-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
                                    <svg className="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fillRule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-blue-900 dark:text-blue-100">{data?.length || 0}</div>
                                <div className="text-sm font-medium text-blue-700 dark:text-blue-300">Data Points</div>
                            </div>
                        </div>
                    </div>

                    {/* Perfect Hours */}
                    <div className="rounded-lg border border-purple-200 bg-gradient-to-r from-purple-50 to-purple-100 p-6 dark:border-purple-700 dark:from-purple-900 dark:to-purple-800">
                        <div className="flex items-center">
                            <div className="flex-shrink-0">
                                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-purple-500">
                                    <svg className="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div className="ml-4">
                                <div className="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                    {processedData.filter((item) => item.uptime_percentage === 100).length}
                                </div>
                                <div className="text-sm font-medium text-purple-700 dark:text-purple-300">Perfect Hours</div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Charts Grid */}
            <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {/* Line Chart */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Uptime Trend</h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <LineChart data={processedData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                            <XAxis dataKey="time" stroke="#6b7280" fontSize={12} tickLine={false} axisLine={false} />
                            <YAxis domain={[95, 100]} stroke="#6b7280" fontSize={12} tickLine={false} axisLine={false} />
                            <Tooltip content={<CustomTooltip />} />
                            <Line
                                type="monotone"
                                dataKey="uptime_percentage"
                                stroke="#10B981"
                                strokeWidth={3}
                                dot={{ fill: '#10B981', strokeWidth: 2, r: 4 }}
                                activeDot={{ r: 6, stroke: '#10B981', strokeWidth: 2 }}
                            />
                        </LineChart>
                    </ResponsiveContainer>
                </div>

                {/* Pie Chart */}
                <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Uptime Distribution</h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <PieChart>
                            <Pie
                                data={pieData}
                                cx="50%"
                                cy="50%"
                                labelLine={false}
                                label={({ name, value }) => `${name}: ${value}%`}
                                outerRadius={80}
                                fill="#8884d8"
                                dataKey="value"
                            >
                                {pieData.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={entry.color} />
                                ))}
                            </Pie>
                            <Tooltip content={<PieTooltip />} />
                            <Legend verticalAlign="bottom" height={36} iconType="circle" />
                        </PieChart>
                    </ResponsiveContainer>
                </div>
            </div>

            {/* Bar Chart */}
            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Hourly Performance</h3>
                <ResponsiveContainer width="100%" height={300}>
                    <BarChart data={processedData} margin={{ top: 20, right: 30, left: 20, bottom: 5 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                        <XAxis dataKey="time" stroke="#6b7280" fontSize={12} tickLine={false} axisLine={false} />
                        <YAxis domain={[0, 100]} stroke="#6b7280" fontSize={12} tickLine={false} axisLine={false} />
                        <Tooltip
                            content={({ active, payload, label }) => {
                                if (active && payload && payload.length) {
                                    return (
                                        <div className="rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                            <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{`Time: ${label}`}</p>
                                            <p className="text-sm text-green-600 dark:text-green-400">{`Uptime: ${payload[0]?.value || 0}%`}</p>
                                            {payload[1] && (
                                                <p className="text-sm text-red-600 dark:text-red-400">{`Downtime: ${payload[1].value}%`}</p>
                                            )}
                                        </div>
                                    );
                                }
                                return null;
                            }}
                        />
                        <Legend />
                        <Bar dataKey="uptime_percentage" fill="#10B981" name="Uptime %" radius={[2, 2, 0, 0]} />
                        <Bar dataKey="downtime_percentage" fill="#EF4444" name="Downtime %" radius={[2, 2, 0, 0]} />
                    </BarChart>
                </ResponsiveContainer>
            </div>

            {/* Status Messages */}
            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Status Summary</h3>
                <div className="space-y-2">
                    {uptimePercentage >= 99.9 ? (
                        <div className="flex items-center rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-700 dark:bg-green-900">
                            <div className="mr-3 h-2 w-2 rounded-full bg-green-500"></div>
                            <span className="font-medium text-green-800 dark:text-green-200">Excellent uptime performance!</span>
                        </div>
                    ) : uptimePercentage >= 95 ? (
                        <div className="flex items-center rounded-lg border border-yellow-200 bg-yellow-50 p-3 dark:border-yellow-700 dark:bg-yellow-900">
                            <div className="mr-3 h-2 w-2 rounded-full bg-yellow-500"></div>
                            <span className="font-medium text-yellow-800 dark:text-yellow-200">Good uptime, room for improvement</span>
                        </div>
                    ) : (
                        <div className="flex items-center rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-700 dark:bg-red-900">
                            <div className="mr-3 h-2 w-2 rounded-full bg-red-500"></div>
                            <span className="font-medium text-red-800 dark:text-red-200">Uptime needs attention</span>
                        </div>
                    )}

                    <div className="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Last updated:{' '}
                        {processedData.length > 0 ? new Date(processedData[processedData.length - 1].fullTime).toLocaleString() : 'No data available'}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default WebsiteStatsChart;
