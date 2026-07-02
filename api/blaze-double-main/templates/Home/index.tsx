"use client";

import { Double } from "./components/Double";
import { Controls } from "./components/Controls";

const Home = () => {
  return (
    <div className="flex flex-col md:flex-row container w-full gap-3  items-center justify-center h-full mx-auto">
      <Controls />
      <Double />
    </div>
  );
};

export default Home;
