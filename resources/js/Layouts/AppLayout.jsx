import Header from "../components/ui/header";
import { usePage } from "@inertiajs/react";
import { FlickeringGrid } from "@/components/ui/flickering-grid";

export default function AppLayout({ title, children }) {
    const { component, url } = usePage();

    const hideHeaderComponents = [
        "DatabaseDown",
        'ResetPassword'
    ];

    const hideHeaderUrls = [
        "/login",
        "/register",
        '/verify-otp',
        "/",
    ];

    const shouldHideHeader =
        hideHeaderComponents.includes(component) ||
        hideHeaderUrls.includes(url);

    return (
        <div className="fixed inset-0 overflow-y-auto bg-[url('/assets/Desktop.png')] w-full h-dvh flex flex-col bg-cover">

            {!shouldHideHeader && <Header />}

            <FlickeringGrid
                className="absolute inset-0 -z-10"
                squareSize={5}
                gridGap={10}
                flickerChance={0.3}
                color="rgb(100, 125 , 255)"
                maxOpacity={0.2}
            />

            <main className="h-dvh">
                {children}
            </main>
        </div>
    );
}
