export const dirTitleRegex = (subject: string): { title: string, year: number } => {
  const regex = new RegExp('^(?<title>.+?)?(?![(\\[])(?:(?:[-_\\W](?<![)\\[!]))*(?<year>(1([89])|20)\\d{2}(?!p|i|\\d+|]|\\W\\d+)))+(\\W+|_|$)(?!\\\\)');
  const match = subject.match(regex);

  return {
    title: match.groups.title,
    year: Number(match.groups.year) || null,
  };
};
