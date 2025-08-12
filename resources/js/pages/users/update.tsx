import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';

type User = {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
};

export default function EditUser() {
  const { props } = usePage<{ user: User }>();
  const user = props.user;

  const [form, setForm] = useState({
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: '',
    is_admin: user.is_admin,
  });

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin/users' },
    { title: `Edit User`, href: `/admin/users/${user.id}/edit` }
  ];

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setForm(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }));
  };

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    router.put(`/admin/users/${user.id}`, form);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Edit - ${user.name}`} />
      <div className="p-4 max-w-md">
        <h1 className="text-xl font-bold mb-4">Edit User</h1>

        <form onSubmit={submit} className="space-y-4">
          <div>
            <label className="block mb-1 font-semibold">Name</label>
            <input
              type="text"
              name="name"
              value={form.name}
              onChange={handleChange}
              required
              className="input"
              autoComplete="name"
            />
          </div>

          <div>
            <label className="block mb-1 font-semibold">Email</label>
            <input
              type="email"
              name="email"
              value={form.email}
              onChange={handleChange}
              required
              className="input"
              autoComplete="email"
            />
          </div>

          <div>
            <label className="block mb-1 font-semibold">Password (leave blank to keep unchanged)</label>
            <input
              type="password"
              name="password"
              value={form.password}
              onChange={handleChange}
              className="input"
              autoComplete="new-password"
            />
          </div>

          <div>
            <label className="block mb-1 font-semibold">Confirm Password</label>
            <input
              type="password"
              name="password_confirmation"
              value={form.password_confirmation}
              onChange={handleChange}
              className="input"
              autoComplete="new-password"
            />
          </div>

          <div className="flex items-center gap-2">
            <input
              type="checkbox"
              id="is_admin"
              name="is_admin"
              checked={form.is_admin}
              onChange={handleChange}
              className="checkbox"
            />
            <label htmlFor="is_admin" className="select-none">Is Admin</label>
          </div>

          <Button type="submit">Update User</Button>
        </form>
      </div>
    </AppLayout>
  );
}
