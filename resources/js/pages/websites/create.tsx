import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import WebsiteForm from '@/components/websites/WebsiteForm';
import { BreadcrumbItem } from '@/types';

type PageProps = {
  subscription: any;
  errors: Record<string, string[]>;
};

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/websites' },
  { title: 'Add Website', href: '/websites/create' }
];

export default function CreateWebsite() {
  const { props } = usePage<PageProps>();
  const errors = props.errors || {};
  const subscription = props.subscription;

  const submit = (data: any) => {
    router.post('/api/websites', data);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Add Website" />
      <div className="p-4">
        <h1 className="text-xl font-bold mb-4">Add New Website</h1>

        {Object.keys(errors).length > 0 && (
          <div className="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">
            <ul className="list-disc pl-4">
              {Object.entries(errors).map(([field, messages]) =>
                messages.map((msg, i) => <li key={`${field}-${i}`}>{msg}</li>)
              )}
            </ul>
          </div>
        )}

        <WebsiteForm onSubmit={submit} subscription={subscription} />
      </div>
    </AppLayout>
  );
}
