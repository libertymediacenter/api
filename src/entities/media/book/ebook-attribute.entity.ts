import { Column, Entity, JoinColumn, OneToMany, PrimaryGeneratedColumn } from 'typeorm';
import { EbookEntity } from './ebook.entity';

@Entity({name: 'ebook_attributes'})
export class EbookAttributeEntity {
  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column('text')
  attribute: string;

  @Column('text')
  value: string | number | boolean;

  @OneToMany(type => EbookEntity, ebook => ebook.attributes)
  @JoinColumn({name: 'ebook_uuid', referencedColumnName: 'uuid'})
  ebook: EbookEntity;
}
