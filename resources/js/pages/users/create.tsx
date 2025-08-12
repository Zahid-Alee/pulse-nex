import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/users' },
    { title: 'Add User', href: '/admin/users/create' },
];

export default function CreateUser() {
    const { errors } = usePage().props as any;

    const [form, setForm] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        is_admin: false,
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value, type, checked } = e.target;
        setForm((prev) => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value,
        }));
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/admin/users', form);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Add User" />
            <div className="max-w-md p-4">
                <h1 className="mb-4 text-xl font-bold">Add New User</h1>

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <label className="mb-1 block font-semibold" htmlFor="name">
                            Name
                        </label>
                        <Input id="name" type="text" name="name" value={form.name} onChange={handleChange} required autoComplete="name" />
                        {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block font-semibold" htmlFor="email">
                            Email
                        </label>
                        <Input id="email" type="email" name="email" value={form.email} onChange={handleChange} required autoComplete="email" />
                        {errors.email && <p className="mt-1 text-sm text-red-600">{errors.email}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block font-semibold" htmlFor="password">
                            Password
                        </label>
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            value={form.password}
                            onChange={handleChange}
                            required
                            autoComplete="new-password"
                        />
                        {errors.password && <p className="mt-1 text-sm text-red-600">{errors.password}</p>}
                    </div>

                    <div>
                        <label className="mb-1 block font-semibold" htmlFor="password_confirmation">
                            Confirm Password
                        </label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            value={form.password_confirmation}
                            onChange={handleChange}
                            required
                            autoComplete="new-password"
                        />
                        {errors.password_confirmation && <p className="mt-1 text-sm text-red-600">{errors.password_confirmation}</p>}
                    </div>

                    {/* <div className="flex items-center gap-2">
                        <Checkbox id="is_admin" name="is_admin" checked={form.is_admin} onChange={handleChange} />
                        <label htmlFor="is_admin" className="select-none">
                            Is Admin
                        </label>
                    </div> */}

                    <Button type="submit">Create User</Button>
                </form>
            </div>
        </AppLayout>
    );
}
