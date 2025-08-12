import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

export default function WebsiteHistoryTable({ history }: { history: any[] }) {
    return (
        <div className="rounded-lg bg-white p-4 shadow dark:bg-gray-900">
            <h2 className="mb-2 text-lg font-bold text-gray-900 dark:text-gray-100">Recent Checks</h2>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead className="text-gray-700 dark:text-gray-300">Checked At</TableHead>
                        <TableHead className="text-gray-700 dark:text-gray-300">Status</TableHead>
                        <TableHead className="text-gray-700 dark:text-gray-300">Response Time</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {history.map((check, i) => (
                        <TableRow key={i}>
                            <TableCell className="font-medium text-gray-900 dark:text-gray-100">{check.checked_at}</TableCell>
                            <TableCell>
                                <span
                                    className={`rounded-full px-2 py-1 text-xs ${
                                        check.status === 'up'
                                            ? 'bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-200'
                                            : check.status === 'down'
                                              ? 'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-200'
                                              : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'
                                    }`}
                                >
                                    {check.status}
                                </span>
                            </TableCell>
                            <TableCell className="text-gray-900 dark:text-gray-100">{check.response_time} ms</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
