import React from 'react';

// Helper function to format datetime to Asia/Karachi timezone
const formatToKarachiTime = (datetime: string) => {
    try {
        const date = new Date(datetime);
        
        // Format to Asia/Karachi timezone with readable format
        return date.toLocaleString('en-PK', {
            timeZone: 'Asia/Karachi',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false // Use 24-hour format
        });
    } catch (error) {
        console.error('Error formatting datetime:', error);
        return datetime; // Fallback to original string
    }
};

// Alternative formatting function with more readable format
const formatToKarachiTimeReadable = (datetime: string) => {
    try {
        const date = new Date(datetime);
        
        return date.toLocaleString('en-US', {
            timeZone: 'Asia/Karachi',
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    } catch (error) {
        console.error('Error formatting datetime:', error);
        return datetime;
    }
};

// Simple formatting function - just date and time in Karachi timezone
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
            hour12: true
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

interface WebsiteHistoryTableProps {
    history: HistoryCheck[];
}

export default function WebsiteHistoryTable({ history }: WebsiteHistoryTableProps) {
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

    return (
        <div className="rounded-lg bg-white p-4 shadow dark:bg-gray-900">
            <div className="mb-4 flex items-center justify-between">
                <h2 className="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Recent Checks
                </h2>
                <span className="text-sm text-gray-500 dark:text-gray-400">
                    Times shown in Pakistan Time (PKT)
                </span>
            </div>
            
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead className="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Checked At
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Status
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Response Time
                            </th>
                            {/* <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                Status Code
                            </th> */}
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        {history.map((check, i) => (
                            <tr key={check.id || i} className="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <div className="text-sm">
                                        <div className="font-medium text-gray-900 dark:text-gray-100">
                                            {formatSimpleKarachiTime(check.checked_at)}
                                        </div>
                                        <div className="text-xs text-gray-500 dark:text-gray-400">
                                            {formatToKarachiTime(check.checked_at)}
                                        </div>
                                    </div>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <div className="flex items-center">
                                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize ${getStatusColor(check)}`}>
                                            <span className={`w-2 h-2 rounded-full mr-1.5 ${
                                                getStatusDisplay(check) === 'up' 
                                                    ? 'bg-green-400' 
                                                    : 'bg-red-400'
                                            }`}></span>
                                            {getStatusDisplay(check)}
                                        </span>
                                    </div>
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {check.response_time ? (
                                        <span className={`font-mono ${
                                            check.response_time < 500 
                                                ? 'text-green-600 dark:text-green-400' 
                                                : check.response_time < 1000 
                                                    ? 'text-yellow-600 dark:text-yellow-400'
                                                    : 'text-red-600 dark:text-red-400'
                                        }`}>
                                            {check.response_time}ms
                                        </span>
                                    ) : (
                                        <span className="text-gray-400 dark:text-gray-500">N/A</span>
                                    )}
                                </td>
                                {/* <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {check.status_code ? (
                                        <span className={`font-mono px-2 py-1 rounded text-xs ${
                                            check.status_code >= 200 && check.status_code < 300
                                                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                                                : check.status_code >= 400 && check.status_code < 500
                                                    ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200'
                                                    : check.status_code >= 500
                                                        ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
                                        }`}>
                                            {check.status_code}
                                        </span>
                                    ) : (
                                        <span className="text-gray-400 dark:text-gray-500">-</span>
                                    )}
                                </td> */}
                            </tr>
                        ))}
                    </tbody>
                </table>
                
                {/* Empty state */}
                {history.length === 0 && (
                    <div className="text-center py-12">
                        <div className="mx-auto h-12 w-12 text-gray-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" className="h-12 w-12">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 className="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No check history</h3>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Check history will appear here once monitoring begins.
                        </p>
                    </div>
                )}
            </div>
            
            {/* Footer with summary if there are checks */}
            {history.length > 0 && (
                <div className="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div className="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <span>Total checks: {history.length}</span>
                        <span>•</span>
                        <span>
                            Uptime: {Math.round((history.filter(check => getStatusDisplay(check) === 'up').length / history.length) * 100)}%
                        </span>
                    </div>
                    <div className="text-xs text-gray-400 dark:text-gray-500">
                        Last updated: {history.length > 0 ? formatSimpleKarachiTime(history[0].checked_at) : 'Never'}
                    </div>
                </div>
            )}
        </div>
    );
}