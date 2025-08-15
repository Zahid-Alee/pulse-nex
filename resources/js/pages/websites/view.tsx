import { Button } from '@/components/ui/button';
import WebsiteHistoryTable from '@/components/websites/WebsiteHistoryTable';
import WebsiteStatsChart from '@/components/websites/WebsiteStatsChart';
import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';

export default function ViewWebsite() {
    const { props } = usePage<{ website: any; stats: any; history: any }>();
    const { website, stats, history } = props;

    const checkNow = () => {
        router.post(`/api/websites/${website.id}/check-now`);
    };

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/websites' },
                { title: website.name, href: `/websites/${website.id}` },
            ]}
        >
            <Head title={website.name} />
            <div className="space-y-6 p-4">
                {/* <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold">{website.name}</h1>
                    <Button onClick={checkNow}>Check Now</Button>
                </div> */}

                <WebsiteStatsChart data={stats.hourly_data} uptimePercentage={stats.uptime_percentage} />
                <WebsiteHistoryTable history={history.data} />
            </div>
        </AppLayout>
    );
}
