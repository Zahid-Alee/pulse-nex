import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <div className="">
            <Head title="Log in" />
            <div className="flex min-h-screen">
                {/* Left Side - Image Section */}
                <div className="relative hidden overflow-hidden lg:flex lg:flex-1">
                    {/* Animated Background */}
                    <div className="absolute inset-0 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700">
                        {/* Floating Animation Elements */}
                        <div className="absolute top-20 left-20 h-72 w-72 animate-pulse rounded-full bg-white/10 blur-3xl"></div>
                        <div className="absolute right-20 bottom-20 h-96 w-96 animate-bounce rounded-full bg-pink-500/20 blur-3xl"></div>
                        <div className="absolute top-1/2 left-1/4 h-64 w-64 animate-ping rounded-full bg-cyan-400/15 blur-2xl"></div>

                        {/* Geometric Shapes Animation */}
                        <div className="absolute top-1/4 right-1/3 h-4 w-4 rotate-45 animate-spin bg-white/30"></div>
                        <div className="absolute bottom-1/3 left-1/4 h-6 w-6 animate-bounce rounded-full bg-yellow-300/40"></div>
                        <div className="absolute top-3/4 right-1/4 h-8 w-8 rotate-12 animate-pulse bg-green-400/30"></div>
                    </div>

                    {/* Content Overlay */}
                    <div className="relative z-10 flex flex-col items-center justify-center p-12 text-white">
                        {/* Image Container - You can replace this with your SVG */}
                        <div className="mb-8 flex w-full items-center justify-center rounded-3xl border border-white/20 bg-white/10 shadow-2xl backdrop-blur-sm">
                            <img src="/assets/images/login.png" alt="" />
                            {/* <div className="text-center">
                                <div className="w-32 h-32 bg-white/20 rounded-full mx-auto mb-4 flex items-center justify-center">
                                    <div className="w-16 h-16 bg-white/30 rounded-lg animate-pulse"></div>
                                </div>
                                <p className="text-sm opacity-80">Add your SVG here</p>
                            </div> */}
                        </div>

                        {/* Welcome Text */}
                        <div className="max-w-md text-center">
                            <h1 className="animate-fade-in mb-4 text-4xl font-bold">Welcome Back!</h1>
                            <p className="animate-fade-in-delay text-lg opacity-90">Sign in to continue your journey with us</p>
                        </div>
                    </div>
                </div>

                {/* Right Side - Form Section */}
                <div className="flex flex-1 items-center justify-center bg-background p-8 lg:p-12">
                    <div className="animate-slide-in w-full max-w-xl space-y-8 rounded-3xl border border-border bg-card p-8 shadow-2xl backdrop-blur-sm">
                        {/* Form Header */}
                        <div className="text-center lg:text-left">
                            <h2 className="text-3xl font-bold tracking-tight text-foreground">Log in to your account</h2>
                            <p className="mt-2 text-sm text-muted-foreground">Enter your email and password below to log in</p>
                        </div>

                        {/* Status Message */}
                        {status && (
                            <div className="animate-fade-in rounded-lg border border-green-200 bg-green-50 p-3 text-center text-sm font-medium text-green-600 dark:border-green-800 dark:bg-green-900/20">
                                {status}
                            </div>
                        )}

                        {/* Form */}
                        <form className="space-y-6" onSubmit={submit}>
                            <div className="space-y-4">
                                {/* Email Field */}
                                <div className="animate-slide-in-delay-1 space-y-2">
                                    <Label htmlFor="email" className="text-sm font-medium">
                                        Email address
                                    </Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        required
                                        autoFocus
                                        tabIndex={1}
                                        autoComplete="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="email@example.com"
                                        className="transition-all duration-200 hover:shadow-md focus:scale-[1.02]"
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                {/* Password Field */}
                                <div className="animate-slide-in-delay-2 space-y-2">
                                    <div className="flex items-center justify-between">
                                        <Label htmlFor="password" className="text-sm font-medium">
                                            Password
                                        </Label>
                                        {canResetPassword && (
                                            <TextLink
                                                href={route('password.request')}
                                                className="text-sm transition-all duration-200 hover:text-primary hover:underline"
                                                tabIndex={5}
                                            >
                                                Forgot password?
                                            </TextLink>
                                        )}
                                    </div>
                                    <Input
                                        id="password"
                                        type="password"
                                        required
                                        tabIndex={2}
                                        autoComplete="current-password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Password"
                                        className="transition-all duration-200 hover:shadow-md focus:scale-[1.02]"
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                {/* Remember Me */}
                                <div className="animate-slide-in-delay-3 flex items-center space-x-3">
                                    <Checkbox
                                        id="remember"
                                        name="remember"
                                        checked={data.remember}
                                        onClick={() => setData('remember', !data.remember)}
                                        tabIndex={3}
                                        className="transition-all duration-200 hover:scale-110"
                                    />
                                    <Label htmlFor="remember" className="cursor-pointer text-sm font-medium">
                                        Remember me
                                    </Label>
                                </div>

                                {/* Submit Button */}
                                <Button
                                    type="submit"
                                    className="animate-slide-in-delay-4 mt-6 w-full transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                    tabIndex={4}
                                    disabled={processing}
                                >
                                    {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                    Log in
                                </Button>
                            </div>

                            {/* Sign Up Link */}
                            <div className="animate-slide-in-delay-5 text-center text-sm text-muted-foreground">
                                Don't have an account?{' '}
                                <TextLink
                                    href={route('register')}
                                    tabIndex={5}
                                    className="font-medium transition-all duration-200 hover:text-primary hover:underline"
                                >
                                    Sign up
                                </TextLink>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {/* Custom Animation Styles */}
            <style jsx>{`
                @keyframes fade-in {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                @keyframes slide-in {
                    from {
                        opacity: 0;
                        transform: translateX(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
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
                .animate-slide-in-delay-1 {
                    animation: slide-in 0.6s ease-out 0.1s both;
                }
                .animate-slide-in-delay-2 {
                    animation: slide-in 0.6s ease-out 0.2s both;
                }
                .animate-slide-in-delay-3 {
                    animation: slide-in 0.6s ease-out 0.3s both;
                }
                .animate-slide-in-delay-4 {
                    animation: slide-in 0.6s ease-out 0.4s both;
                }
                .animate-slide-in-delay-5 {
                    animation: slide-in 0.6s ease-out 0.5s both;
                }
            `}</style>
        </div>
    );
}
