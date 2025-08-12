import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { usePage } from '@inertiajs/react';
import React from 'react';

type WebsiteFormProps = {
    initialValues?: Partial<{
        name: string;
        url: string;
        check_interval: number;
        timeout: number;
        is_active: boolean;
    }>;
    subscription?: {
        plan_name: string;
        max_websites?: number;
        websites_count?: number;
        check_interval?: number;
    };
    onSubmit: (data: any) => void;
    submitting?: boolean;
};

export default function WebsiteForm({ initialValues = {}, subscription, onSubmit, submitting }: WebsiteFormProps) {
    const { props } = usePage<{ errors?: Record<string, string[]> }>();
    const errors = props.errors || {};

    console.log('subscription', subscription);

    const [form, setForm] = React.useState({
        name: initialValues.name || '',
        url: initialValues.url || '',
        check_interval: initialValues.check_interval || 60,
        timeout: initialValues.timeout || 5,
        is_active: initialValues.is_active ?? true,
    });

    const handleChange = (key: string, value: any) => {
        setForm((prev) => ({ ...prev, [key]: value }));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        // Subscription validation logic before submitting
        if (subscription?.max_websites && subscription.websites_count !== undefined) {
            if (subscription.websites_count >= subscription.max_websites) {
                alert(`Your current plan (${subscription.plan_name}) allows a maximum of ${subscription.max_websites} websites.`);
                return;
            }
        }

        if (subscription?.check_interval && form.check_interval < subscription.check_interval) {
            alert(`Your plan requires a minimum check interval of ${subscription.check_interval} seconds.`);
            return;
        }

        onSubmit(form);
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            {/* Website Name */}
            <div>
                <Label htmlFor="name">Website Name</Label>
                <Input
                    id="name"
                    value={form.name}
                    onChange={(e) => handleChange('name', e.target.value)}
                    required
                    className={errors.name ? 'border-red-500' : ''}
                />
                {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name[0]}</p>}
            </div>

            {/* URL */}
            <div>
                <Label htmlFor="url">URL</Label>
                <Input
                    id="url"
                    type="url"
                    value={form.url}
                    onChange={(e) => handleChange('url', e.target.value)}
                    required
                    className={errors.url ? 'border-red-500' : ''}
                />
                {errors.url && <p className="mt-1 text-sm text-red-600">{errors.url[0]}</p>}
            </div>

            {/* Check Interval */}
            <div>
                <Label htmlFor="check_interval">Check Interval (seconds)</Label>
                <Input
                    id="check_interval"
                    type="number"
                    min={subscription?.check_interval || 60}
                    max={3600}
                    value={form.check_interval}
                    onChange={(e) => handleChange('check_interval', parseInt(e.target.value))}
                    className={errors.check_interval ? 'border-red-500' : ''}
                />
                {errors.check_interval && <p className="mt-1 text-sm text-red-600">{errors.check_interval[0]}</p>}
                {subscription?.check_interval && (
                    <p className="mt-1 text-xs text-gray-500">Minimum allowed by your plan: {subscription.check_interval} seconds</p>
                )}
            </div>

            {/* Timeout */}
            <div>
                <Label htmlFor="timeout">Timeout (seconds)</Label>
                <Input
                    id="timeout"
                    type="number"
                    min={5}
                    max={120}
                    value={form.timeout}
                    onChange={(e) => handleChange('timeout', parseInt(e.target.value))}
                    className={errors.timeout ? 'border-red-500' : ''}
                />
                {errors.timeout && <p className="mt-1 text-sm text-red-600">{errors.timeout[0]}</p>}
            </div>

            {/* Active Checkbox */}
            <div className="flex items-center gap-2">
                <input type="checkbox" checked={form.is_active} onChange={(e) => handleChange('is_active', e.target.checked)} />
                <Label>Active</Label>
            </div>

            {subscription && (
                <div className="rounded border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700">
                    <p>
                        Plan: <strong>{subscription.plan_name}</strong>
                    </p>
                    {subscription.max_websites && (
                        <p>
                            Websites: {subscription.websites_count}/{subscription.max_websites}
                        </p>
                    )}
                </div>
            )}

            {/* Submit Button */}
            <Button type="submit" disabled={submitting}>
                {submitting ? 'Saving...' : 'Save Website'}
            </Button>
        </form>
    );
}
