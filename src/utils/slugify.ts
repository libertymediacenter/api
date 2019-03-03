import * as slug from 'slug';

export const slugify = (string: string) => {
  return slug(string, {
    lower: true,
    remove: /[*+~.()'"!:@]/g,
  });
};
