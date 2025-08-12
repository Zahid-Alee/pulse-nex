import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';

type User = {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
};

export default function EditUser() {
  const { props } = usePage<{ user: User; errors: Record<string, string> }>();
  const user = props.user;
  const errors = props.errors || {};

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
            <label htmlFor="name" className="block mb-1 font-semibold">Name</label>
            <Input
              id="name"
              type="text"
              name="name"
              value={form.name}
              onChange={handleChange}
              required
              autoComplete="name"
            />
            {errors.name && <p className="text-red-600 text-sm mt-1">{errors.name}</p>}
          </div>

          <div>
            <label htmlFor="email" className="block mb-1 font-semibold">Email</label>
            <Input
              id="email"
              type="email"
              name="email"
              value={form.email}
              onChange={handleChange}
              required
              autoComplete="email"
            />
            {errors.email && <p className="text-red-600 text-sm mt-1">{errors.email}</p>}
          </div>

          <div>
            <label htmlFor="password" className="block mb-1 font-semibold">
              Password (leave blank to keep unchanged)
            </label>
            <Input
              id="password"
              type="password"
              name="password"
              value={form.password}
              onChange={handleChange}
              autoComplete="new-password"
            />
            {errors.password && <p className="text-red-600 text-sm mt-1">{errors.password}</p>}
          </div>

          <div>
            <label htmlFor="password_confirmation" className="block mb-1 font-semibold">
              Confirm Password
            </label>
            <Input
              id="password_confirmation"
              type="password"
              name="password_confirmation"
              value={form.password_confirmation}
              onChange={handleChange}
              autoComplete="new-password"
            />
            {errors.password_confirmation && (
              <p className="text-red-600 text-sm mt-1">{errors.password_confirmation}</p>
            )}
          </div>

          {/* <div className="flex items-center gap-2">
            <Checkbox
              id="is_admin"
              name="is_admin"
              checked={form.is_admin}
              onChange={handleChange}
            />
            <label htmlFor="is_admin" className="select-none">Is Admin</label>
          </div> */}

          <Button type="submit">Update User</Button>
        </form>
      </div>
    </AppLayout>
  );
}
