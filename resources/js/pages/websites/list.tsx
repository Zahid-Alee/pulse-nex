import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import { EyeIcon, Pencil, Plus, Trash2 } from 'lucide-react';

import { useToast } from '@/components/ui/use-toast';
import React from 'react';

export default function WebsitesList() {
    const { props } = usePage<{ websites: any[]; errors?: Record<string, string>; flash?: { success?: string } }>();
    const { toast } = useToast();
    const websites = props.websites;
    const errors = props.errors || {};
    const flash = props.flash || {};

    console.log('websites', websites);

    React.useEffect(() => {
        if (errors.check_now) {
            // console.log('rendering error');
            // toast({
            //     title: 'Please wait',
            //     description: errors.check_now,
            //     variant: 'destructive',
            // });
            alert(errors.check_now);
        }

        if (flash.success) {
            toast({
                title: 'Success',
                description: flash.success,
                variant: 'default',
            });
        }
    }, [errors, flash]);

    const deleteWebsite = (id: number) => {
        if (!confirm('Are you sure you want to delete this website?')) return;
        router.delete(`/api/websites/${id}`);
    };

    const checkNow = (id: number) => {
        router.visit(`/websites/${id}/check-now`, { method: 'post' });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/websites' }]}>
            <Head title="Websites" />

            <div className="p-4">
                {/* Header */}
                <div className="mb-4 flex items-center justify-between">
                    <h1 className="text-xl font-bold">Websites</h1>
                    <Button onClick={() => router.visit('/websites/create')} className="flex items-center gap-2">
                        <Plus size={16} /> Add Website
                    </Button>
                </div>

                {/* Card wrapping the table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Website List</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>URL</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Check Interval (sec)</TableHead>
                                    <TableHead>Last Checked</TableHead>
                                    <TableHead>Timeout (sec)</TableHead>
                                    <TableHead>Active</TableHead>
                                    <TableHead className="w-[320px] text-center">Actions</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                {websites.length > 0 ? (
                                    websites.map((w) => (
                                        <TableRow key={w.id}>
                                            <TableCell>{w.name}</TableCell>
                                            <TableCell className="text-blue-600 underline">{w.url}</TableCell>
                                            <TableCell>
                                                <Badge variant={w.status === 'up' ? 'default' : 'destructive'}>
                                                    {w.status === 'up' ? 'Up' : 'Down'}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>{w.check_interval}</TableCell>
                                            <TableCell>{w.last_checked_at ?? '—'}</TableCell>
                                            <TableCell>{w.timeout}</TableCell>
                                            <TableCell>
                                                <Badge variant={w.is_active ? 'default' : 'destructive'}>{w.is_active ? 'Active' : 'Inactive'}</Badge>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex flex-wrap justify-center gap-2">
                                                    {/* View Website */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="outline" size="sm" onClick={() => router.visit(`/websites/${w.id}`)}>
                                                                    <EyeIcon size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>View Website</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    {/* Edit */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    variant="secondary"
                                                                    size="sm"
                                                                    onClick={() => router.visit(`/websites/${w.id}/edit`)}
                                                                >
                                                                    <Pencil size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>Edit Website</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    {/* Delete */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="destructive" size="sm" onClick={() => deleteWebsite(w.id)}>
                                                                    <Trash2 size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>Delete Website</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    {/* Check Now */}
                                                    {/* <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="default" size="sm" onClick={() => checkNow(w.id)}>
                                                                    <RefreshCw size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>Check Now</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider> */}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={8} className="py-6 text-center text-gray-500">
                                            No websites found.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
