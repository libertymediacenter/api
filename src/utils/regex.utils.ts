export const dirTitleRegex = (subject: string): { title: string, year: number } => {
  let regex = new RegExp('^(?<title>.+?)?(?![(\\[])(?:(?:[-_\\W](?<![)\\[!]))*(?<year>(1([89])|20)\\d{2}(?!p|i|\\d+|]|\\W\\d+)))+(\\W+|_|$)(?!\\\\)');
  let match = subject.match(regex);

  if (match) {
    return {
      title: match.groups.title,
      year: Number(match.groups.year) || null,
    };
  }

  return {
    title: subject,
    year: null,
  };
};
