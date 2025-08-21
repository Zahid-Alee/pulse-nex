import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import { Eye, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';

export default function ContactsList() {
    const { props } = usePage<{ contacts: any[] }>();
    const contacts = props.contacts;

    const [selectedContactId, setSelectedContactId] = useState<number | null>(null);
    const [contactDetails, setContactDetails] = useState<any>(null);
    const [loading, setLoading] = useState(false);

    const openContactModal = (id: number) => {
        setSelectedContactId(id);
    };

    const closeContactModal = () => {
        setSelectedContactId(null);
        setContactDetails(null);
    };

    const handleDelete = (id: number) => {
        if (confirm('Are you sure you want to delete this contact?')) {
            router.delete(`/admin/contacts/${id}`);
        }
    };

    useEffect(() => {
        if (selectedContactId !== null) {
            setLoading(true);
            fetch(`/admin/contacts/${selectedContactId}`)
                .then((res) => res.json())
                .then((data) => {
                    setContactDetails(data);
                    setLoading(false);
                    // Optionally, refresh the contacts list or update the status locally
                })
                .catch(() => {
                    setLoading(false);
                    alert('Failed to load contact details.');
                });
        }
    }, [selectedContactId]);

    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/admin/contacts' }]}>
            <Head title="Contact Queries" />

            <div className="p-4">
                <div className="mb-4 flex items-center justify-between">
                    <h1 className="text-xl font-bold">Contact Queries</h1>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Contact Queries List</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="w-[160px] text-center">Received At</TableHead>
                                    <TableHead className="w-[120px] text-center">Actions</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                {contacts.length > 0 ? (
                                    contacts.map((contact) => (
                                        <TableRow key={contact.id}>
                                            <TableCell>{contact.name}</TableCell>
                                            <TableCell>{contact.email}</TableCell>
                                            <TableCell>{contact.subject}</TableCell>
                                            <TableCell>
                                                <Badge
                                                    variant={
                                                        contact.status === 'replied'
                                                            ? 'default'
                                                            : contact.status === 'read'
                                                              ? 'secondary'
                                                              : 'destructive'
                                                    }
                                                >
                                                    {contact.status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-center">{contact.created_at}</TableCell>
                                            <TableCell>
                                                <div className="flex justify-center gap-2">
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="outline" size="sm" onClick={() => openContactModal(contact.id)}>
                                                                    <Eye size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>View Query</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>

                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <Button variant="destructive" size="sm" onClick={() => handleDelete(contact.id)}>
                                                                    <Trash2 size={16} />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>Delete Query</TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={6} className="py-6 text-center text-gray-500">
                                            No contact queries found.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            {/* Modal to show contact details */}
            <Dialog open={selectedContactId !== null} onOpenChange={closeContactModal}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Contact Query Details</DialogTitle>
                    </DialogHeader>

                    {loading ? (
                        <p>Loading...</p>
                    ) : contactDetails ? (
                        <div className="space-y-4">
                            <p>
                                <strong>Name:</strong> {contactDetails.name}
                            </p>
                            <p>
                                <strong>Email:</strong> {contactDetails.email}
                            </p>
                            <p>
                                <strong>Subject:</strong> {contactDetails.subject}
                            </p>
                            <div>
                                <strong>Message:</strong>
                                <pre className="mt-1 whitespace-pre-wrap">{contactDetails.message}</pre>
                            </div>

                            <p>
                                <strong>Status:</strong>{' '}
                                <Badge
                                    variant={
                                        contactDetails.status === 'replied'
                                            ? 'default'
                                            : contactDetails.status === 'read'
                                              ? 'secondary'
                                              : 'destructive'
                                    }
                                >
                                    {contactDetails.status}
                                </Badge>
                            </p>
                            <p>
                                <strong>Received At:</strong> {contactDetails.created_at}
                            </p>
                        </div>
                    ) : (
                        <p>No details available.</p>
                    )}

                    <DialogFooter>
                        <Button variant="outline" onClick={closeContactModal}>
                            Close
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
