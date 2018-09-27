drop table if exists category;
create table category
(
    id int unsigned not null auto_increment comment 'ID',
    cat_name varchar(255) not null comment '分类名称',
    parent_id int unsigned not null default 0 comment '上级ID',
    path varchar(255) not null default '-' comment '所有上级分类的ID',
    primary key (id)
)engine=InnoDB comment='分类表';

drop table if exists brand;
create table brand
(
    id int unsigned not null auto_increment comment 'ID',
    brand_name varchar(255) not null comment '品牌名称',
    logo varchar(255) not null comment 'LOGO',
    primary key (id)
)engine=InnoDB comment='品牌表';

drop table if exists goods;
create table goods
(
    id int unsigned not null auto_increment comment 'ID',
    goods_name varchar(255) not null comment '商品名称',
    logo varchar(255) not null comment 'LOGO',
    is_on_sale enum('y','n') not null default 'y' comment '是否上架',
    description longtext not null comment '商品描述',
    cat1_id int unsigned not null comment '一级分类ID',
    cat2_id int unsigned not null comment '二级分类ID',
    cat3_id int unsigned not null comment '三级分类ID',
    brand_id int unsigned not null comment '品牌ID',
    created_at datetime not null default current_timestamp comment '添加时间',
    primary key (id)
)engine=InnoDB comment='商品表';

drop table if exists goods_attribute;
create table goods_attribute
(
    id int unsigned not null auto_increment comment 'ID',
    attr_name varchar(255) not null comment '属性名称',
    attr_value varchar(255) not null comment '属性值',
    goods_id int unsigned not null comment '所属的商品ID',
    primary key (id)
)engine=InnoDB comment='商品属性表';

drop table if exists goods_image;
create table goods_image
(
    id int unsigned not null auto_increment comment 'ID',
    goods_id int unsigned not null comment '所属的商品ID',
    path varchar(255) not null comment '图片的路径',
    primary key (id)
)engine=InnoDB comment='商品图片表';

drop table if exists goods_sku;
create table goods_sku
(
    id int unsigned not null auto_increment comment 'ID',
    goods_id int unsigned not null comment '所属的商品ID',
    sku_name varchar(255) not null comment 'SKU名称',
    stock int unsigned not null comment '库存量',
    price decimal(10,2) not null comment '价格',
    primary key (id)
)engine=InnoDB comment='商品SKU表';
