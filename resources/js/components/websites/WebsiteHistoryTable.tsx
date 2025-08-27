import { useState } from 'react';

// Helper function to format datetime to Asia/Karachi timezone
const formatToKarachiTime = (datetime: string) => {
    try {
        const date = new Date(datetime);
        return date.toLocaleString('en-PK', {
            timeZone: 'Asia/Karachi',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });
    } catch (error) {
        console.error('Error formatting datetime:', error);
        return datetime;
    }
};

const formatSimpleKarachiTime = (datetime: string) => {
    try {
        const date = new Date(datetime);
        return date.toLocaleString('en-US', {
            timeZone: 'Asia/Karachi',
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
        });
    } catch (error) {
        console.error('Error formatting datetime:', error);
        return datetime;
    }
};

interface HistoryCheck {
    id?: number;
    checked_at: string;
    status?: string;
    is_up?: boolean;
    response_time?: number;
    status_code?: number;
    error_message?: string;
}

interface ApiResponse {
    data: HistoryCheck[];
    meta: {
        total: number;
        filtered_total: number;
        limit: number;
        offset: number;
        uptime_percentage: number;
        has_more: boolean;
    };
}

interface WebsiteHistoryTableProps {
    history: HistoryCheck[];
    websiteId: number;
    apiEndpoint?: string;
}

interface Filters {
    status: 'all' | 'up' | 'down';
    dateRange: 'all' | '24h' | '7d' | '30d' | 'custom';
    customFrom?: string;
    customTo?: string;
}

export default function WebsiteHistoryTable({ history, websiteId, apiEndpoint = '/websites' }: WebsiteHistoryTableProps) {
    const [displayHistory, setDisplayHistory] = useState<HistoryCheck[]>(history);
    const [showAllHistory, setShowAllHistory] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [filters, setFilters] = useState<Filters>({
        status: 'all',
        dateRange: 'all',
    });
    const [meta, setMeta] = useState<ApiResponse['meta'] | null>(null);
    const [hasMore, setHasMore] = useState(false);

    const getStatusDisplay = (check: HistoryCheck) => {
        if (check.status) {
            return check.status.toLowerCase();
        }
        return check.is_up ? 'up' : 'down';
    };

    const getStatusColor = (check: HistoryCheck) => {
        const status = getStatusDisplay(check);
        switch (status) {
            case 'up':
                return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200';
            case 'down':
                return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200';
            case 'timeout':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200';
            case 'error':
                return 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
        }
    };

    const getDateRangeParams = (range: string, customFrom?: string, customTo?: string) => {
        const now = new Date();
        const params: { from?: string; to?: string } = {};

        switch (range) {
            case '24h':
                params.from = new Date(now.getTime() - 24 * 60 * 60 * 1000).toISOString();
                break;
            case '7d':
                params.from = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000).toISOString();
                break;
            case '30d':
                params.from = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000).toISOString();
                break;
            case 'custom':
                if (customFrom) params.from = customFrom;
                if (customTo) params.to = customTo;
                break;
        }

        return params;
    };

    const fetchHistory = async (offset = 0, reset = true) => {
        setLoading(true);
        setError(null);

        try {
            const params = new URLSearchParams();
            params.append('limit', '50');
            params.append('offset', offset.toString());

            if (filters.status !== 'all') {
                params.append('status', filters.status);
            }

            const dateParams = getDateRangeParams(filters.dateRange, filters.customFrom, filters.customTo);
            if (dateParams.from) params.append('from', dateParams.from);
            if (dateParams.to) params.append('to', dateParams.to);

            const response = await fetch(`${apiEndpoint}/${websiteId}/history?${params}`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data: ApiResponse = await response.json();

            if (reset) {
                setDisplayHistory(data.data);
            } else {
                setDisplayHistory((prev) => [...prev, ...data.data]);
            }

            setMeta(data.meta);
            setHasMore(data.meta.has_more);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to fetch history');
        } finally {
            setLoading(false);
        }
    };

    const handleShowAllHistory = () => {
        setShowAllHistory(true);
        fetchHistory(0, true);
    };

    const handleFilterChange = (newFilters: Partial<Filters>) => {
        const updatedFilters = { ...filters, ...newFilters };
        setFilters(updatedFilters);

        if (showAllHistory) {
            fetchHistory(0, true);
        }
    };

    const loadMore = () => {
        if (meta && hasMore) {
            fetchHistory(meta.offset + meta.limit, false);
        }
    };

    const resetToDefault = () => {
        setShowAllHistory(false);
        setDisplayHistory(history);
        setFilters({ status: 'all', dateRange: 'all' });
        setMeta(null);
        setHasMore(false);
    };

    const currentUptime = meta
        ? meta.uptime_percentage
        : Math.round((displayHistory.filter((check) => getStatusDisplay(check) === 'up').length / displayHistory.length) * 100);

    return (
        <div className="rounded-lg bg-white p-4 shadow dark:bg-gray-900">
            <div className="mb-4 flex items-center justify-between">
                <h2 className="text-lg font-bold text-gray-900 dark:text-gray-100">{showAllHistory ? 'All History' : 'Recent Checks'}</h2>
                <div className="flex items-center gap-3">
                    <span className="text-sm text-gray-500 dark:text-gray-400">Times shown in Pakistan Time (PKT)</span>
                    {!showAllHistory && (
                        <button
                            onClick={handleShowAllHistory}
                            className="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200"
                        >
                            View All History
                        </button>
                    )}
                    {showAllHistory && (
                        <button
                            onClick={resetToDefault}
                            className="text-sm font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            Show Recent Only
                        </button>
                    )}
                </div>
            </div>

            {/* Filters */}
            {/* {showAllHistory && (
                <div className="mb-4">
                    <Filters
                        filters={filters}
                        onChange={handleFilterChange}
                    />
                </div>
            )} */}

            {/* Error State */}
            {error && (
                <div className="mb-4 rounded-md border border-red-200 bg-red-50 p-3 dark:border-red-700 dark:bg-red-900/20">
                    <p className="text-sm text-red-700 dark:text-red-400">{error}</p>
                </div>
            )}

            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead className="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                Checked At
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                Status
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300">
                                Response Time
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                        {displayHistory.map((check, i) => (
                            <tr key={check.id || i} className="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <div className="text-sm">
                                        <div className="font-medium text-gray-900 dark:text-gray-100">
                                            {formatSimpleKarachiTime(check.checked_at)}
                                        </div>
                                        <div className="text-xs text-gray-500 dark:text-gray-400">{formatToKarachiTime(check.checked_at)}</div>
                                    </div>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <div className="flex items-center">
                                        <span
                                            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ${getStatusColor(check)}`}
                                        >
                                            <span
                                                className={`mr-1.5 h-2 w-2 rounded-full ${
                                                    getStatusDisplay(check) === 'up' ? 'bg-green-400' : 'bg-red-400'
                                                }`}
                                            ></span>
                                            {getStatusDisplay(check)}
                                        </span>
                                    </div>
                                </td>
                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {check.response_time ? (
                                        <span
                                            className={`font-mono ${
                                                check.response_time < 500
                                                    ? 'text-green-600 dark:text-green-400'
                                                    : check.response_time < 1000
                                                      ? 'text-yellow-600 dark:text-yellow-400'
                                                      : 'text-red-600 dark:text-red-400'
                                            }`}
                                        >
                                            {check.response_time}ms
                                        </span>
                                    ) : (
                                        <span className="text-gray-400 dark:text-gray-500">N/A</span>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                {/* Loading State */}
                {loading && (
                    <div className="py-8 text-center">
                        <div className="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-gray-900 dark:border-gray-100"></div>
                        <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">Loading history...</p>
                    </div>
                )}

                {/* Load More Button */}
                {showAllHistory && hasMore && !loading && (
                    <div className="py-4 text-center">
                        <button
                            onClick={loadMore}
                            className="rounded-md border border-blue-300 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 hover:text-blue-800 dark:border-blue-600 dark:text-blue-400 dark:hover:bg-blue-900/20 dark:hover:text-blue-200"
                        >
                            Load More
                        </button>
                    </div>
                )}

                {/* Empty state */}
                {displayHistory.length === 0 && !loading && (
                    <div className="py-12 text-center">
                        <div className="mx-auto h-12 w-12 text-gray-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" className="h-12 w-12">
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={1}
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                        </div>
                        <h3 className="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {showAllHistory ? 'No results found' : 'No check history'}
                        </h3>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {showAllHistory ? 'Try adjusting your filters.' : 'Check history will appear here once monitoring begins.'}
                        </p>
                    </div>
                )}
            </div>

            {/* Footer with summary */}
            {displayHistory.length > 0 && (
                <div className="mt-4 flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
                    <div className="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <span>{showAllHistory && meta ? `${meta.filtered_total} total checks` : `${displayHistory.length} recent checks`}</span>
                        <span>•</span>
                        <span>Uptime: {currentUptime}%</span>
                    </div>
                    <div className="text-xs text-gray-400 dark:text-gray-500">
                        Last updated: {displayHistory.length > 0 ? formatSimpleKarachiTime(displayHistory[0].checked_at) : 'Never'}
                    </div>
                </div>
            )}
        </div>
    );
}