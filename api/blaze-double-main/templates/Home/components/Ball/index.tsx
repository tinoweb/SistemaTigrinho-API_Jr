type BallProps = {
  color: string;
  textColor: string;
  number: number;
  size: number | string;
};

export const Ball = ({ color, textColor, number, size }: BallProps) => (
  <div
    className={`h-[${size}px] w-[${size}px] flex items-center justify-center rounded-md ${color}`}
  >
    <p className={`rounded-full font-bold ${color} ${textColor}`}>{number}</p>
  </div>
);
