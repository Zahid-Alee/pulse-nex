import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import { Calendar, ExternalLink, EyeIcon, Globe, Mail, Shield } from 'lucide-react';

export default function ViewUser() {
    const { props } = usePage<{ user: any; websites: any[] }>();
    const { user, websites } = props;

    const initials = user.name
        .split(' ')
        .map((n: string) => n[0])
        .join('')
        .toUpperCase();

    // const deleteWebsite = (id: number) => {
    //     if (!confirm('Are you sure you want to delete this website?')) return;
    //     router.delete(`/api/websites/${id}`);
    // };

    // const checkNow = (id: number) => {
    //     router.visit(`/websites/${id}/check-now`, { method: 'post' });
    // };

    return (
        <AppLayout breadcrumbs={[{ title: 'Users', href: '/admin/users' }, { title: user.name }]}>
            <Head title={user.name} />

            <div className="space-y-6 p-4">
                {/* Profile Card */}
                <Card>
                    <CardHeader className="flex flex-col items-center gap-4 sm:flex-row sm:items-start">
                        <Avatar className="h-20 w-20">
                            <AvatarImage src={user.avatar_url || ''} alt={user.name} />
                            <AvatarFallback className="bg-primary/10 font-bold text-primary">{initials}</AvatarFallback>
                        </Avatar>
                        <div className="flex-1 space-y-1">
                            <CardTitle className="text-xl font-bold">{user.name}</CardTitle>
                            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                <Mail className="h-4 w-4" /> {user.email}
                            </div>
                            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                <Shield className="h-4 w-4" /> {user.is_admin ? 'Admin' : 'User'}
                            </div>
                        </div>
                        <Badge variant={user.plan_name && user.plan_name !== 'FREE' ? 'default' : 'secondary'}>{user.plan_name}</Badge>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        <Separator />
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <Calendar className="h-4 w-4" /> Joined {user.created_at}
                        </div>
                    </CardContent>
                </Card>

                {/* Websites Table */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Globe className="h-5 w-5" /> Websites
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {websites.length > 0 ? (
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
                                    {websites.map((site) => (
                                        <TableRow key={site.id}>
                                            <TableCell>{site.name}</TableCell>
                                            <TableCell className="text-blue-600 underline">{site.url}</TableCell>
                                            <TableCell>
                                                <Badge variant={site.status === 'up' ? 'default' : 'destructive'}>
                                                    {site.status === 'up' ? 'Up' : 'Down'}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>{site.check_interval}</TableCell>
                                            <TableCell>{site.last_checked_at ?? '—'}</TableCell>
                                            <TableCell>{site.timeout}</TableCell>
                                            <TableCell>
                                                <Badge variant={site.is_active ? 'default' : 'destructive'}>
                                                    {site.is_active ? 'Active' : 'Inactive'}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex flex-wrap justify-center gap-2">
                                                    {/* View */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="outline" size="sm" onClick={() => window.open(site.url, '_blank')}>
                                                                    <ExternalLink size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>View Link</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    variant="outline"
                                                                    size="sm"
                                                                    onClick={() => router.visit(`/websites/${site.id}/`)}
                                                                >
                                                                    <EyeIcon size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>View Link</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        ) : (
                            <p className="text-sm text-muted-foreground">No websites found.</p>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
