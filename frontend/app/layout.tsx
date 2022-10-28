import { FC, PropsWithChildren } from "react";

const RootLayout: FC<PropsWithChildren> = ({ children }) => {
    return (
        <html lang="en">
            <head>
                <title>Next.js</title>
            </head>
            <body>{children}</body>
        </html>
    );
};

export default RootLayout;
