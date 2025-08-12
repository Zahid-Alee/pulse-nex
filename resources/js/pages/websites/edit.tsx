import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import WebsiteForm from '@/components/websites/WebsiteForm';
import { BreadcrumbItem } from '@/types';

type Website = {
  id: number;
  name: string;
  url: string;
  check_interval: number;
  timeout: number;
  is_active: boolean;
};

export default function EditWebsite() {
  const { props } = usePage<{ website: Website }>();
  const website = props.website;

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/websites' },
    { title: 'Edit Website', href: `/websites/${website.id}/edit` }
  ];

  const submit = (data: any) => {
    router.put(`/api/websites/${website.id}`, data);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Edit - ${website.name}`} />
      <div className="p-4">
        <h1 className="text-xl font-bold mb-4">Edit Website</h1>
        <WebsiteForm initialValues={website} onSubmit={submit} />
      </div>
    </AppLayout>
  );
}
