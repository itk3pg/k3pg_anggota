<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Setting_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_grup($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_grup, nm_grup";

        $this->db->select($select);

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("nm_grup");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "nm_grup";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get("s_grup");
    }

    public function insert_grup($data)
    {
        $id_grup = get_maxid("s_grup", "id_grup");

        $set_data = array(
            "id_grup" => $id_grup,
            "nm_grup" => strtoupper($this->db->escape_str($data['nm_grup'])),
        );

        return $this->db->set($set_data)->insert("s_grup");
    }

    public function update_grup($data, $id)
    {
        $set_data = array(
            // "id_grup" => $id_grup,
            "nm_grup" => strtoupper($this->db->escape_str($data['nm_grup'])),
        );

        return $this->db->set($set_data)->where("id_grup", $id)->update("s_grup");
    }

    public function delete_grup($data)
    {
        return $this->db->where("id_grup", $data['id_grup'])->delete("s_grup");
    }

    public function get_user($numrows = 0, $cari = "", $order = "", $offset = "", $limit = "")
    {
        $select = ($numrows) ? "count(*) numrows" : "id_user, nama, username, a.id_grup, b.nm_grup";

        $this->db->select($select)
            ->from("s_user a")
            ->join("s_grup b", "a.id_grup = b.id_grup");

        if (is_array($cari) and $cari['value'] != "") {
            $set_cari = isset($cari['field'][0]) ? $cari['field'] : array("nama", "username", "b.nm_grup");

            $this->db->group_start();

            foreach ($set_cari as $key => $value) {
                $this->db->or_like($value, $cari['value']);
            }

            $this->db->group_end();
        }

        $set_order = ($order) ? $order : "nama";

        $this->db->order_by($set_order);

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get();
    }

    public function insert_user($data)
    {
        $id_user = get_maxid("s_user", "id_user");

        $set_data = array(
            "id_user"  => $id_user,
            "nama"     => $this->db->escape_str($data['nama']),
            "username" => strtoupper($this->db->escape_str($data['username'])),
            "passwd"   => $data['passwd'],
            "id_grup"  => $data['id_grup'],
        );

        return $this->db->set($set_data)->insert("s_user");
    }

    public function update_user($data, $id)
    {
        $set_data = array(
            // "id_user"  => $id_user,
            "nama"     => $this->db->escape_str($data['nama']),
            "username" => strtoupper($this->db->escape_str($data['username'])),
            // "passwd"   => $data['passwd'],
            "id_grup"  => $data['id_grup'],
        );

        if ($data['passwd'] != "") {
            $set_data['passwd'] = $data['passwd'];
        }

        return $this->db->set($set_data)->where("id_user", $id)->update("s_user");
    }

    public function delete_user($data)
    {
        return $this->db->where("id_user", $data['id_user'])->delete("s_user");
    }

    public function get_menu($cari = "", $sort = "", $order = "", $offset = "", $limit = "", $numrows = "")
    {
        $query_select = ($numrows) ? " count(*) numrows " : " id_menu, jns_menu, nm_menu, link ";

        if (is_array($cari) and $cari['value'] != "") {
            $set_field   = isset($cari['field'][0]) ? $cari['field'] : array("nm_menu", "link");
            $join_field  = implode(" like '%" . $cari['value'] . "%' or ", $set_field);
            $query_where = "and (" . $join_field . " like '%" . $cari['value'] . "%') ";
        } else {
            $query_where = "";
        }

        $query_sort = ($sort) ? " order by " . $sort . " " . $order : " order by jns_menu, nm_menu ";

        $query_limit = ($limit) ? " limit " . $offset . ", " . $limit : "";

        $query = "select " . $query_select . " from s_menu where 1 " . $query_where . $query_sort . $query_limit;

        return $this->db->query($query);
    }

    public function insert_menu($data)
    {
        $id = get_maxid("s_menu", "id_menu");

        $query = "insert s_menu set id_menu = '" . $id . "', jns_menu = '" . $data['jns_menu'] . "', nm_menu = \"" . $data['nm_menu'] . "\", link = '" . $data['link'] . "'";

        return $this->db->simple_query($query);
    }

    public function update_menu($data, $id)
    {
        $query = "update s_menu set nm_menu = '" . $data['nm_menu'] . "', link = '" . $data['link'] . "' "
            . "where id_menu = '" . $id . "'";

        return $this->db->simple_query($query);
    }

    public function delete_menu($data)
    {
        $query = "delete from s_menu where id_menu = '" . $data['id_menu'] . "'";

        return $this->db->simple_query($query);
    }

    public function get_inactive_menu($id_grup, $level, $id_parent0, $id_parent1, $id_parent2)
    {
        $query_jenis_menu = ($level < 2) ? " or (jns_menu = '1' and id_menu not in (select id_menu from s_akses where id_grup = '" . $id_grup . "' and id_parent0 = '" . $id_parent0 . "' and id_parent1 = '" . $id_parent1 . "' and id_parent2 = '" . $id_parent2 . "')) " : "";

        $query = "select id_menu, jns_menu, nm_menu, link from s_menu "
            . "where (jns_menu = '2' and id_menu not in (select id_menu from s_akses where id_grup = '" . $id_grup . "'))  " . $query_jenis_menu
            . "order by jns_menu, nm_menu";

        return $this->db->query($query);
    }

    public function get_active_menu($id_grup, $id_parent0 = "0", $id_parent1 = "0", $id_parent2 = "0")
    {
        // $query_parent = ($id_parent0 != "" and $id_parent1 != "" and $id_parent2 != "") ? " and b.id_parent0 = '" . $id_parent0 . "' and b.id_parent1 = '" . $id_parent1 . "' and b.id_parent2 = '" . $id_parent2 . "' " : "";
        $query_parent = " and b.id_parent0 = '" . $id_parent0 . "' and b.id_parent1 = '" . $id_parent1 . "' and b.id_parent2 = '" . $id_parent2 . "' ";

        $query = "select a.id_menu, a.jns_menu, a.nm_menu, a.link, b.id_grup, b.id_parent, b.id_parent0, b.id_parent1, b.id_parent2, b.sort from s_menu a join s_akses b on a.id_menu = b.id_menu where id_grup = '" . $id_grup . "' " . $query_parent . "order by sort";

        // baca($query);

        return $this->db->query($query);
    }

    public function tambah_active_menu($data)
    {
        $query_sort = "select ifnull(max(sort), 0) + 1 sort from s_akses
                        where id_grup = '" . $data['id_grup'] . "' and id_parent0 = '" . $data['id_parent0'] . "' and id_parent1 = '" . $data['id_parent1'] . "' and id_parent2 = '" . $data['id_parent2'] . "'";

        $sort = $this->db->query($query_sort)->row(0)->sort;

        $query = "insert into s_akses
                    set id_grup = '" . $data['id_grup'] . "', id_menu = '" . $data['id_menu'] . "', id_parent0 = '" . $data['id_parent0'] . "', id_parent1 = '" . $data['id_parent1'] . "', id_parent2 = '" . $data['id_parent2'] . "', sort = '" . $sort . "'";

        return $this->db->simple_query($query);
    }

    public function hapus_active_menu($data)
    {
        // $query = "select id_menu
        //             from s_akses
        //             where id_parent = '" . $data['id_menu'] . "' and id_grup = '" . $data['id_grup'] . "' ";

        // $data_menu = $this->db->query($query)->result_array();

        // foreach ($data_menu as $value) {
        //     $query = "delete from s_akses
        //         where id_parent = '" . $value['id_menu'] . "' and id_grup = '" . $data['id_grup'] . "'";

        //     $this->db->query($query);
        // }

        // $query = "delete from s_akses
        //             where id_grup = '" . $data['id_grup'] . "' and id_parent = '" . $data['id_menu'] . "'";

        // $this->db->query($query);

        if ($data['id_parent0'] == "0" and $data['id_parent1'] == "0" and $data['id_parent2'] == "0") {
            $query = "delete from s_akses where id_grup = '" . $data['id_grup'] . "' and  id_parent0 = '" . $data['id_parent0'] . "' and id_parent1 = '" . $data['id_menu'] . "'";

            $this->db->simple_query($query);
        } else if ($data['id_parent0'] == "0" and $data['id_parent1'] != "0" and $data['id_parent2'] == "0") {
            $query = "delete from s_akses where id_grup = '" . $data['id_grup'] . "' and  id_parent0 = '" . $data['id_parent0'] . "' and id_parent1 = '" . $data['id_parent1'] . "' and id_parent2 = '" . $data['id_menu'] . "'";

            $this->db->simple_query($query);
        }

        $query = "delete from s_akses
                    where id_grup = '" . $data['id_grup'] . "' and id_menu = '" . $data['id_menu'] . "' and id_parent0 = '" . $data['id_parent0'] . "' and id_parent1 = '" . $data['id_parent1'] . "' and id_parent2 = '" . $data['id_parent2'] . "'";

        return $this->db->simple_query($query);
    }

    public function simpan_active_menu($data)
    {
        $query = "update s_akses
                    set sort = '" . $data['sort'] . "'
                    where id_grup = '" . $data['id_grup'] . "' and id_menu = '" . $data['id_menu'] . "' and id_parent0 = '" . $data['id_parent0'] . "' and id_parent1 = '" . $data['id_parent1'] . "' and id_parent2 = '" . $data['id_parent2'] . "'";

        // baca($query);

        return $this->db->simple_query($query);
    }

    public function copy_menu($grup, $dari_grup)
    {
        $query = "delete from s_akses where id_grup = '" . $grup . "'";

        $this->db->simple_query($query);

        $query = "insert into s_akses (id_grup, id_menu, id_parent0, id_parent1, id_parent2, sort)
                    select '" . $grup . "' grup, id_menu, id_parent0, id_parent1, id_parent2, sort
                    from s_akses
                    where id_grup = '" . $dari_grup . "'";

        return $this->db->simple_query($query);
    }

}
