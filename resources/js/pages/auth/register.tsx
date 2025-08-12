import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type RegisterForm = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm<Required<RegisterForm>>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <div>
            <Head title="Create an account" />
            <div className="min-h-screen flex">
                {/* Left Side - Image Section */}
                <div className="hidden lg:flex lg:flex-1 relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700">
                        <div className="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                        <div className="absolute bottom-20 right-20 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl animate-bounce"></div>
                        <div className="absolute top-1/2 left-1/4 w-64 h-64 bg-cyan-400/15 rounded-full blur-2xl animate-ping"></div>
                        <div className="absolute top-1/4 right-1/3 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div>
                        <div className="absolute bottom-1/3 left-1/4 w-6 h-6 bg-yellow-300/40 rounded-full animate-bounce"></div>
                        <div className="absolute top-3/4 right-1/4 w-8 h-8 bg-green-400/30 rotate-12 animate-pulse"></div>
                    </div>

                    <div className="relative z-10 flex flex-col justify-center items-center p-12 text-white">
                        <div className="w-full mb-8 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center border border-white/20 shadow-2xl">
                            <img src="/assets/images/login.png" alt="" />
                        </div>
                        <div className="text-center max-w-md">
                            <h1 className="text-4xl font-bold mb-4 animate-fade-in">Join Us Today!</h1>
                            <p className="text-lg opacity-90 animate-fade-in-delay">
                                Create your account and start your journey with us
                            </p>
                        </div>
                    </div>
                </div>

                {/* Right Side - Form Section */}
                <div className="flex-1 flex items-center justify-center p-8 lg:p-12 bg-background">
                    <div className="w-full max-w-xl space-y-8 animate-slide-in bg-card shadow-2xl rounded-3xl p-8 border border-border backdrop-blur-sm">
                        <div className="text-center lg:text-left">
                            <h2 className="text-3xl font-bold tracking-tight text-foreground">
                                Create an account
                            </h2>
                            <p className="mt-2 text-sm text-muted-foreground">
                                Enter your details below to sign up
                            </p>
                        </div>

                        <form className="space-y-6" onSubmit={submit}>
                            <div className="space-y-4">
                                <div className="space-y-2 animate-slide-in-delay-1">
                                    <Label htmlFor="name">Name</Label>
                                    <Input
                                        id="name"
                                        type="text"
                                        required
                                        autoFocus
                                        tabIndex={1}
                                        autoComplete="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        disabled={processing}
                                        placeholder="Full name"
                                        className="transition-all duration-200 focus:scale-[1.02] hover:shadow-md"
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="space-y-2 animate-slide-in-delay-2">
                                    <Label htmlFor="email">Email address</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        required
                                        tabIndex={2}
                                        autoComplete="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        disabled={processing}
                                        placeholder="email@example.com"
                                        className="transition-all duration-200 focus:scale-[1.02] hover:shadow-md"
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                <div className="space-y-2 animate-slide-in-delay-3">
                                    <Label htmlFor="password">Password</Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        required
                                        tabIndex={3}
                                        autoComplete="new-password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        disabled={processing}
                                        placeholder="Password"
                                        className="transition-all duration-200 focus:scale-[1.02] hover:shadow-md"
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="space-y-2 animate-slide-in-delay-4">
                                    <Label htmlFor="password_confirmation">Confirm password</Label>
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        required
                                        tabIndex={4}
                                        autoComplete="new-password"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        disabled={processing}
                                        placeholder="Confirm password"
                                        className="transition-all duration-200 focus:scale-[1.02] hover:shadow-md"
                                    />
                                    <InputError message={errors.password_confirmation} />
                                </div>

                                <Button
                                    type="submit"
                                    className="w-full mt-6 transition-all duration-300 hover:scale-[1.02] hover:shadow-lg animate-slide-in-delay-5"
                                    tabIndex={5}
                                    disabled={processing}
                                >
                                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                    Create account
                                </Button>
                            </div>

                            <div className="text-center text-sm text-muted-foreground animate-slide-in-delay-5">
                                Already have an account?{' '}
                                <TextLink
                                    href={route('login')}
                                    tabIndex={6}
                                    className="font-medium hover:underline transition-all duration-200 hover:text-primary"
                                >
                                    Log in
                                </TextLink>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <style jsx>{`
                @keyframes fade-in {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes slide-in {
                    from { opacity: 0; transform: translateX(30px); }
                    to { opacity: 1; transform: translateX(0); }
                }
                .animate-fade-in {
                    animation: fade-in 0.8s ease-out;
                }
                .animate-fade-in-delay {
                    animation: fade-in 0.8s ease-out 0.2s both;
                }
                .animate-slide-in {
                    animation: slide-in 0.6s ease-out;
                }
                .animate-slide-in-delay-1 { animation: slide-in 0.6s ease-out 0.1s both; }
                .animate-slide-in-delay-2 { animation: slide-in 0.6s ease-out 0.2s both; }
                .animate-slide-in-delay-3 { animation: slide-in 0.6s ease-out 0.3s both; }
                .animate-slide-in-delay-4 { animation: slide-in 0.6s ease-out 0.4s both; }
                .animate-slide-in-delay-5 { animation: slide-in 0.6s ease-out 0.5s both; }
            `}</style>
        </div>
    );
}
