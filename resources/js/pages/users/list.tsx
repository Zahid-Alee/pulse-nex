import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import { Eye, Pencil, Plus, Trash } from 'lucide-react';
import { useState } from 'react';

export default function UsersList() {
    const { props } = usePage<{ users: any[] }>();
    const users = props.users;

    const [selectedUser, setSelectedUser] = useState<any>(null);
    const [selectedPlan, setSelectedPlan] = useState<string>('');
    const [amount, setAmount] = useState<string>('0');
    const [monitorsLimit, setMonitorsLimit] = useState<string>('1');
    const [checkInterval, setCheckInterval] = useState<string>('5');

    const deleteUser = (id: number) => {
        if (!confirm('Are you sure you want to delete this user?')) return;
        router.delete(`/admin/users/${id}`);
    };

    const openChangePlanModal = (user) => {
        setSelectedUser(user);
        setSelectedPlan(user.plan_name || 'FREE');
        setAmount(String(user.amount ?? 0));
        setMonitorsLimit(String(user.monitors_limit ?? 1));
        setCheckInterval(String(user.check_interval ?? 5));
    };

    const confirmChangePlan = () => {
        if (!selectedUser || !selectedPlan) return;
        router.post(
            `/admin/users/${selectedUser.id}/upgrade`,
            {
                plan_name: selectedPlan,
                amount: parseFloat(amount),
                monitors_limit: parseInt(monitorsLimit, 10),
                check_interval: parseInt(checkInterval, 10),
            },
            {
                preserveScroll: true,
                onSuccess: () => setSelectedUser(null),
            },
        );
    };

    const editUser = (id: number) => {
        router.visit(`/admin/users/${id}/edit`);
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/admin/users' }]}>
            <Head title="Users" />

            <div className="p-4">
                {/* Header */}
                <div className="mb-4 flex items-center justify-between">
                    <h1 className="text-xl font-bold">Users</h1>
                    <Button onClick={() => router.visit('/admin/users/create')} className="flex items-center gap-2">
                        <Plus size={16} /> Add User
                    </Button>
                </div>

                {/* Card wrapping the table */}
                <Card>
                    <CardHeader>
                        <CardTitle>User List</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Active Plan</TableHead>
                                    <TableHead className="w-[320px] text-center">Actions</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                {users.length > 0 ? (
                                    users.map((user) => (
                                        <TableRow key={user.id}>
                                            <TableCell>{user.name}</TableCell>
                                            <TableCell>{user.email}</TableCell>
                                            <TableCell>
                                                <Badge variant={user.plan_name && user.plan_name !== 'FREE' ? 'default' : 'secondary'}>
                                                    {user.plan_name ?? 'FREE'}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex flex-wrap justify-center gap-2">
                                                    {/* View */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button
                                                                    variant="outline"
                                                                    size="sm"
                                                                    onClick={() => router.visit(`/admin/users/${user.id}`)}
                                                                >
                                                                    <Eye size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>View User</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    {/* Edit */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="secondary" size="sm" onClick={() => editUser(user.id)}>
                                                                    <Pencil size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>Edit User</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    {/* Delete */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="destructive" size="sm" onClick={() => deleteUser(user.id)}>
                                                                    <Trash size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>Delete User</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    {/* Change Plan */}
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="default" size="sm" onClick={() => openChangePlanModal(user)}>
                                                                    Change Plan
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>Change User Plan</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={4} className="py-6 text-center text-gray-500">
                                            No users found.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            {/* Change Plan Dialog */}
            <Dialog open={!!selectedUser} onOpenChange={() => setSelectedUser(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Change Plan for {selectedUser?.name}</DialogTitle>
                    </DialogHeader>

                    <div className="space-y-4">
                        <Select value={selectedPlan} onValueChange={(value) => setSelectedPlan(value)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select a plan" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="FREE">Free</SelectItem>
                                <SelectItem value="Pro">Pro</SelectItem>
                                <SelectItem value="Business">Business</SelectItem>
                            </SelectContent>
                        </Select>

                        <div>
                            <label className="mb-1 block text-sm font-medium">Amount (USD)</label>
                            <Input type="number" value={amount} onChange={(e) => setAmount(e.target.value)} />
                        </div>

                        <div>
                            <label className="mb-1 block text-sm font-medium">Monitors Limit</label>
                            <Input type="number" value={monitorsLimit} onChange={(e) => setMonitorsLimit(e.target.value)} />
                        </div>

                        <div>
                            <label className="mb-1 block text-sm font-medium">Check Interval (mins)</label>
                            <Input type="number" value={checkInterval} onChange={(e) => setCheckInterval(e.target.value)} />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" onClick={() => setSelectedUser(null)}>
                            Cancel
                        </Button>
                        <Button onClick={confirmChangePlan} disabled={!selectedPlan}>
                            Save Changes
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
