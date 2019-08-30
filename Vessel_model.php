<?php if (! defined('BASEPATH')) { exit('No direct script access allowed');
}

    
class Vessel_model extends CI_Model
{

    /**
     * Developer Name : Harmeet Singh
     *
     * Comapny Name : HigrooveSystems 
     *
     * Create Date : 27-10-2016
     */
    
    private $userid;
    private $cocode;
    function __construct()
    {
        parent::__construct();        
        $this->load->library('session');
        $this->userid=$this->session->userdata('USERID');
        $this->cocode=$this->session->userdata('COCODE');
    } 
    
    
    public function getVesselData()
    {
        
        $q=$this->input->get('q');
        $type=$this->input->get('type');
        
        $this->db->select('*');
        $this->db->from('udt_VesselMaster WITH (NOLOCK)');
        $this->db->join('udt_VesselType', 'udt_VesselType.ID=udt_VesselMaster.VesselTypeID');
        if($type=='vessel') {
            $this->db->like('VesselName', $q, 'after');        
            $this->db->or_like('VesselExName', $q, 'after');
        }else{
            $this->db->like('IMONumber', $q, 'after');    
        }    
        $q=$this->db->get();
        return $q->result();
    }
    
    public function getVesselData1()
    {
        $q=$this->input->post('key');
        $this->db->select('*');
        $this->db->from('udt_VesselMaster WITH (NOLOCK)');
        $this->db->join('udt_VesselType', 'udt_VesselType.ID=udt_VesselMaster.VesselTypeID');
        
        $this->db->like('VesselName', $q, 'after');        
        $this->db->or_like('VesselExName', $q, 'after');
        
        $query=$this->db->get();
        return $query->result();
    }
    
    
    public function checkDuplicateImo()
    {
        
        $imono=$this->input->get('imono');
        $q=$this->db->get_where('udt_VesselMaster', array('IMONumber'=>$imono));
        return $q->row();
    }
    
    public function saveVesselData()
    {
        
        extract($this->input->post());
        //$this->output->set_output(json_encode($_POST));
        /* if($selectvessel!=''){            
        $vessel=$selectvessel;
        }elseif($vesselnameis!=''){
        $vessel=$vesselnameis;
        }elseif($vesselname!=''){
        $vessel=$vesselname;
        }
        
        if($imonumberis!=''){            
        $imo=$imonumberis;
        }elseif($selectvesselbyimo!=''){
        $imo=$selectvesselbyimo;
        }elseif($imonumber!=''){
        $imo=$imonumber;
        } */
        /* 
        $data=array('CoCode'=>C_COCODE,
        'AuctionID'=>$AuctionId,
        'SelectVesselBy'=>$vesselby,
        'VesselName'=>$vessel,
        'IMO'=>$imo,
        'VesselCurrentName'=>$vesselcurrentname,
        'VesselChangeNameDate'=>date('Y-m-d',strtotime($vesselchangedate)),
        'FirstLoadPortDate'=>date('Y-m-d',strtotime($lparrivaldate)),
        'LastDisPortDate'=>date('Y-m-d',strtotime($dparrivaldate)),
        'LOA'=>$loa,
        'Beam'=>$beam,
        'Draft'=>$draft,
        'DeadWeight'=>$deadweight,
        'Dispalcement'=>$lightweight, 
        'Source'=>$vrsource, 
        'Rating'=>$vrrating, 
        'RatingDate'=>date('Y-m-d',strtotime($vratingdate)), 
        'SourceType'=>$issource, 
        'VettingSource'=>$sourceofvetting, 
        'Deficiency'=>$pscdeficiency, 
        'DeficiencyCompDate'=>date('Y-m-d',strtotime($pscdefcompdate)), 
        'DetentionFlag'=>$pscdetention, 
        'CommentAuction'=>$cargointernalcomments, 
        'CommentInvitee'=>$cargodisplaycomments, 
        'DetentionDate'=>date('Y-m-d',strtotime($pscdetentiondate)), 
        'DetentionLiftedFlag'=>$closingdatestoinclude, 
        'DetentionLiftedDate'=>date('Y-m-d',strtotime($pscdetentionlifteddate)), 
        'DetentionLiftExpectedDate'=>date('Y-m-d',strtotime($epscdetentionlifteddate)), 
        'DetentionLiftExpectedDate'=>date('Y-m-d',strtotime($epscdetentionlifteddate)), 
        'UserID'=>$UserID,
        'UserDate'=>date('Y-m-d H:i:s')
        ); */
        if($cargointernalcomments=='undefined') {
            $CommentAuction='';
        }else{
            $CommentAuction=$cargointernalcomments;
        }
        if($cargodisplaycomments=='undefined') {
            $CommentInvitee='';
        }else{
            $CommentInvitee=$cargodisplaycomments;
        }
        
        $data=array('CoCode'=>C_COCODE,
        'AuctionID'=>$AuctionId,
        'CommentAuction'=>$CommentAuction, 
        'CommentInvitee'=>$CommentInvitee, 
        'UserID'=>$UserID,
        'UserDate'=>date('Y-m-d H:i:s')
        );
        //print_r($data); die;
        $this->db->insert('udt_AU_Vessel', $data);
        //$q=$this->db->insert('udt_AU_Vessel_History',$data);
        //return $q;
    }
    
    
    
    public function getVesselDataByAuction()
    {
        $AuctionId=$this->input->get('AuctionId');
        $q=$this->db->get_where('udt_AU_Vessel', array('AuctionID'=>$AuctionId));
        return $q->row();
    }
    /* 
    public function getVesselDataByResponse(){
    $ResponseID=$this->input->get('ResponseID');
    $q=$this->db->get_where('udt_AU_Vessel',array('ResponseID'=>$ResponseID));
    return $q->row();
    } */
    
    public function getVesselDataByResponse()
    {
        $ResponseID=$this->input->get('ResponseID');
        $ResponseVesselID=$this->input->get('ResponseVesselID');
        
        $this->db->select('udt_AU_ResponseVessel.*, udt_EntityMaster.EntityName, udt_EntityMaster.AssociateCompanyID, udt_CountryMaster.Code as C_Code, udt_CountryMaster.Description as C_Description, udt_StateMaster.Code as S_Code, udt_StateMaster.Description as S_Description, udt_AUM_Freight.EntityID as InviteeEntityID');
        $this->db->from('udt_AU_ResponseVessel');
        $this->db->join('udt_EntityMaster', 'udt_EntityMaster.ID=udt_AU_ResponseVessel.DisponentOwnerID', 'left');
        $this->db->join('udt_CountryMaster', 'udt_CountryMaster.ID=udt_AU_ResponseVessel.CountryID', 'left');
        $this->db->join('udt_StateMaster', 'udt_StateMaster.ID=udt_AU_ResponseVessel.StateID', 'left');
        $this->db->join('udt_AUM_Freight', 'udt_AUM_Freight.ResponseID=udt_AU_ResponseVessel.ResponseID', 'left');
        $this->db->where('ResponseVesselID', $ResponseVesselID);
        $query=$this->db->get();
        //$query=$this->db->get_where('udt_AU_ResponseVessel',array('ResponseVesselID'=>$ResponseVesselID));
        return $query->row();
    }
    
    public function addInviteesData()
    {
        $priority=$this->input->get('priority');
        $AuctionId=$this->input->get('AuctionId');
        $UserID=$this->input->get('UserID');
        $EntityID=$this->input->get('EntityID');
        $this->db->trans_start();
        $q=$this->db->get_where('udt_AUM_Invitees', array('AuctionID'=>$AuctionId));
        if($q->num_rows()>0) {
            $this->db->query("DELETE FROM COPS_Admin.udt_AUM_Invitees WHERE AuctionID='$AuctionId'");
            $this->db->query("DELETE FROM COPS_Admin.udt_AUM_Invitees_H WHERE AuctionID='$AuctionId'");
        } 
        
        $this->db->select('udt_AUM_Invitee_Master.InviteeID, udt_AUM_Invitee_Master.CoCode, udt_AUM_Invitee_Master.InviteeRole, udt_AUM_Invitee_Master.PriorityStatus, udt_AUM_Invitee_Master.QuoteLimitFlag, udt_AUM_Invitee_Master.QuoteLimitValue, udt_UserMaster.EntityID, udt_UserMaster.ID');
        $this->db->from('udt_AUM_Invitee_Master');
        $this->db->join('udt_UserMaster', 'udt_UserMaster.ID=udt_AUM_Invitee_Master.ForUserID', 'Left');
        $this->db->where('udt_AUM_Invitee_Master.RecordOwner', $EntityID);
        if($priority!='P0') {
            $p=(int)substr($priority, -1, 1);
            for($i=1; $i<=$p;$i++){
                $p1[]='P'.$i;
            }
            $this->db->where_in('udt_AUM_Invitee_Master.PriorityStatus', $p1);
        }
        $this->db->where('udt_AUM_Invitee_Master.PrimeRole', 1);
        $this->db->where('udt_AUM_Invitee_Master.InviteeStatus', 1);
        $query=$this->db->get();
        $result=$query->result();
        
        $this->db->select('*');
        $this->db->from('udt_AU_Counter');
        $querycounter=$this->db->get();
        $NewCounter=$querycounter->row()->CounterNo+1;
        
        foreach($result as $rows){
            $q=$this->getCompanyName($rows->EntityID);
            $data1=array('CoCode'=>$rows->CoCode,
            'UserMasterID'=>$rows->ID,
            'AuctionID'=>$AuctionId,
            'Company'=>$q->EntityName,
            'EntityID'=>$rows->EntityID,
            'InviteeRole'=>$rows->InviteeRole,
            'QuoteLimitFlag'=>$rows->QuoteLimitFlag,
            'QuoteLimitValue'=>$rows->QuoteLimitValue,
            'Since'=>'',
            'AdverseComments'=>'',
            'Comments'=>'',                    
            'InvPriorityStatus'=>$rows->PriorityStatus,                    
            'RowStatus'=>'1',
            'RowCounter'=>$NewCounter,
            'UserID'=>$UserID,
            'UserDate'=>date('Y-m-d H:i:s')            
            );
            $this->db->insert('udt_AUM_Invitees_H', $data1);    
                
            $data=array('CoCode'=>$rows->CoCode,
            'UserMasterID'=>$rows->ID,
            'AuctionID'=>$AuctionId,
            'Company'=>$q->EntityName,
            'EntityID'=>$rows->EntityID,
            'InviteeRole'=>$rows->InviteeRole,
            'QuoteLimitFlag'=>$rows->QuoteLimitFlag,
            'QuoteLimitValue'=>$rows->QuoteLimitValue,
            'Since'=>'',
            'AdverseComments'=>'',
            'Comments'=>'',                    
            'InvPriorityStatus'=>$rows->PriorityStatus,                    
            'UserID'=>$UserID,
            'UserDate'=>date('Y-m-d H:i:s')            
            );
            $this->db->insert('udt_AUM_Invitees', $data);            
        }
        
        $query="delete from cops_admin.udt_AU_BusinessProcessAuctionWise where AuctionID='$AuctionId' and BussinessType=2 ";
        $this->db->query($query);
        
        $this->db->select('*');
        $this->db->from('udt_AUM_Invitees');
        $this->db->where('AuctionID', $AuctionId);
        $qry=$this->db->get();
        $rslt=$qry->result();
        
        $this->db->select('*');
        $this->db->from('udt_AUM_BusinessProcess');
        $this->db->where('finalization_completed_by', '2');
        $this->db->where('RecordOwner', $EntityID);
        $this->db->where('status', 1);
        $bqry=$this->db->get();
        $brslt=$bqry->result();
        
        foreach($rslt as $rw){
            foreach($brslt as $brw){
                if($brw->name_of_process==5 && $rw->InviteeRole !=6) {
                    continue;
                } else {
                    $data=array(
                    'BPID'=>$brw->BPID,
                    'AuctionID'=>$AuctionId,
                    'UserList'=>$rw->UserMasterID,
                    'Status'=>1,
                    'BussinessType'=>2,
                    'UserID'=>$UserID,
                    'UserDate'=>date('Y-m-d')
                    );
                    $this->db->insert('udt_AU_BusinessProcessAuctionWise', $data);
                }
            }
        }
        
        $this->db->update('udt_AU_Counter', array('CounterNo'=>$NewCounter));
        $this->db->trans_complete();
    }
    
    public function updateInviteesData()
    {
        $priority=$this->input->get('priority');
        $AuctionId=$this->input->get('AuctionId');
        $UserID=$this->input->get('UserID');
        $EntityID=$this->input->get('EntityID');
        $this->db->trans_start();
        
        $a_data=array(
        'auctionStatus'=>'P',
        'auctionExtendedStatus'=>'',
        'UserID'=>$UserID,
        'UserDate'=>date('Y-m-d H:i:s')
        );
        $this->db->where('AuctionID', $AuctionId);
        $this->db->update('udt_AU_Auctions', $a_data);
        
        
        $q=$this->db->get_where('udt_AUM_Invitees', array('AuctionID'=>$AuctionId));
        if($q->num_rows()>0) {
            $this->db->query("DELETE FROM COPS_Admin.udt_AUM_Invitees WHERE AuctionID='$AuctionId'");
        } 
         
        $this->db->select('udt_AUM_Invitee_Master.InviteeID, udt_AUM_Invitee_Master.CoCode, udt_AUM_Invitee_Master.InviteeRole, udt_AUM_Invitee_Master.QuoteLimitFlag, udt_AUM_Invitee_Master.QuoteLimitValue, udt_AUM_Invitee_Master.PriorityStatus, udt_UserMaster.EntityID, udt_UserMaster.ID');
        $this->db->from('udt_AUM_Invitee_Master');
        $this->db->join('udt_UserMaster', 'udt_UserMaster.ID=udt_AUM_Invitee_Master.ForUserID', 'Left');
        $this->db->where('udt_AUM_Invitee_Master.RecordOwner', $EntityID);
        if($priority!='P0') {
            $p=(int)substr($priority, -1, 1);
            for($i=1; $i<=$p;$i++){
                $p1[]='P'.$i;
            } 
            $this->db->where_in('udt_AUM_Invitee_Master.PriorityStatus', $p1);
        }
        $this->db->where('udt_AUM_Invitee_Master.PrimeRole', 1);
        $this->db->where('udt_AUM_Invitee_Master.InviteeStatus', 1);
        $query=$this->db->get();
        $result=$query->result();
        
        $this->db->select('*');
        $this->db->from('udt_AU_Counter');
        $querycounter=$this->db->get();
        $NewCounter=$querycounter->row()->CounterNo+1;

        foreach($result as $rows){
            $q=$this->getCompanyName($rows->EntityID);
            $data1=array(
            'CoCode'=>$rows->CoCode,
            'UserMasterID'=>$rows->ID,
            'AuctionID'=>$AuctionId,
            'Company'=>$q->EntityName,
            'EntityID'=>$rows->EntityID,
            'InviteeRole'=>$rows->InviteeRole,
            'QuoteLimitFlag'=>$rows->QuoteLimitFlag,
            'QuoteLimitValue'=>$rows->QuoteLimitValue,
            'Since'=>'',
            'AdverseComments'=>'',
            'Comments'=>'',                    
            'InvPriorityStatus'=>$rows->PriorityStatus,                    
            'RowStatus'=>'2',
            'RowCounter'=>$NewCounter,
            'UserID'=>$UserID,
            'UserDate'=>date('Y-m-d H:i:s')            
            );
            $this->db->insert('udt_AUM_Invitees_H', $data1);    
            
            $data=array('CoCode'=>$rows->CoCode,
            'UserMasterID'=>$rows->ID,
            'AuctionID'=>$AuctionId,
            'Company'=>$q->EntityName,
            'EntityID'=>$rows->EntityID,
            'InviteeRole'=>$rows->InviteeRole,
            'QuoteLimitFlag'=>$rows->QuoteLimitFlag,
            'QuoteLimitValue'=>$rows->QuoteLimitValue,
            'Since'=>'',
            'AdverseComments'=>'',
            'Comments'=>'',                    
            'InvPriorityStatus'=>$rows->PriorityStatus,                    
            'UserID'=>$UserID,
            'UserDate'=>date('Y-m-d H:i:s')            
            );
            $this->db->insert('udt_AUM_Invitees', $data);        
        }
        
        $query="delete from cops_admin.udt_AU_BusinessProcessAuctionWise where AuctionID='$AuctionId' and BussinessType=2 ";
        $this->db->query($query);
        
        $this->db->select('*');
        $this->db->from('udt_AUM_Invitees');
        $this->db->where('AuctionID', $AuctionId);
        $qry=$this->db->get();
        $rslt=$qry->result();
        
        $this->db->select('*');
        $this->db->from('udt_AUM_BusinessProcess');
        $this->db->where('finalization_completed_by', '2');
        $this->db->where('RecordOwner', $EntityID);
        $this->db->where('status', 1);
        $bqry=$this->db->get();
        $brslt=$bqry->result();
        
        foreach($rslt as $rw){
            foreach($brslt as $brw){
                if($brw->name_of_process==5 && $rw->InviteeRole !=6) {
                    continue;
                } else {
                    $data=array(
                    'BPID'=>$brw->BPID,
                    'AuctionID'=>$AuctionId,
                    'UserList'=>$rw->UserMasterID,
                    'Status'=>1,
                    'BussinessType'=>2,
                    'UserID'=>$UserID,
                    'UserDate'=>date('Y-m-d')
                    );
                    $this->db->insert('udt_AU_BusinessProcessAuctionWise', $data);
                }
            }
        }
        
        //$query="delete from cops_admin.udt_AU_BusinessProcessAuctionWise where AuctionID='$AuctionId' and BussinessType=2 and UserList not in($users)";
        //$this->db->query($query);
        
        $this->db->update('udt_AU_Counter', array('CounterNo'=>$NewCounter));
        
        $this->db->trans_complete();
    }
    
    public function updateComments()
    {
        $cargointernalcomments=$this->input->post('cargointernalcomments');
        $cargodisplaycomments=$this->input->post('cargodisplaycomments');
        $priority=$this->input->post('priority');
        $AuctionId=$this->input->post('AuctionId');
        $UserID=$this->input->post('UserID');
        extract($this->input->post());
        
        if($cargointernalcomments=='undefined') {
            $AdverseComments='';
        }else{
            $AdverseComments=$cargointernalcomments;
        }
        
        if($cargodisplaycomments=='undefined') {
            $Comments='';
        }else{
            $Comments=$cargodisplaycomments;
        }
        
        $this->db->where('AuctionID', $AuctionId);
        $this->db->update(
            'udt_AUM_Invitees', array(
                        'UserID'=>$UserID,
                        'AdverseComments'=>$AdverseComments,
                        'Comments'=>$Comments
                        )
        );
                        
                        
        $data1=array(
        'DocumentType'=>$typeofdocument,
        'Title'=>$NameorTitleofdocumentattached,
        'ToDisplay'=>$Documenttobedisplayinauctionprocess,
        'ToDisplayInvitee'=>$Documenttobedisplaytoinvitee
        );
        $this->db->where('AuctionID', $AuctionId);
        $this->db->where('AuctionSection', $type);
        $this->db->update('udt_AUM_Documents', $data1); 
    
    }
    
    public function getCompanyName($EntityID)
    {
        $q=$this->db->get_where('udt_EntityMaster', array('ID'=>$EntityID));
        return $q->row();
    }
    
    public function getInvitees()
    {        
        $AuctionId=$this->input->get('AuctionId');
        $this->db->select('InvID,udt_AUM_Invitees.EntityID, InvPriorityStatus, FirstName, LastName, EntityName,udt_AddressMaster.Email as emailid, udt_EntityType.Description as RoleDescription, QuoteLimitFlag,QuoteLimitValue,udt_EntityMaster.AssociateCompanyID');
        $this->db->from('udt_AUM_Invitees');
        $this->db->join('udt_UserMaster', 'udt_UserMaster.ID=udt_AUM_Invitees.UserMasterID');
        $this->db->join('udt_AddressMaster', 'udt_AddressMaster.ID = udt_UserMaster. OfficialAddressID');
        $this->db->join('udt_EntityMaster', 'udt_EntityMaster.ID=udt_AUM_Invitees.EntityID');
        $this->db->join('udt_EntityType', 'udt_EntityType.ID=udt_AUM_Invitees.InviteeRole', 'left');
    
        $this->db->where('AuctionID', $AuctionId);
        $q=$this->db->get();
        return $q->result();
    }
    
    public function deleteInvitees()
    {
        $AuctionId=$this->input->get('AuctionId');
        $invid=$this->input->get('invid');
        $invid=explode(',', $invid);
        $this->db->where('AuctionID', $AuctionId);
        $this->db->where_in('InvID', $invid);
        $this->db->delete('udt_AUM_Invitees');
    }
    
    public function getInviteesData()
    {
        
        $AuctionId=$this->input->get('AuctionId');
        $this->db->select('*');
        $this->db->from('udt_AUM_Invitees');
        $this->db->where('AuctionID', $AuctionId);
        $this->db->order_by('InvPriorityStatus', 'DESC');
        $query=$this->db->get();
        return $query->row();
        /* $q=$this->db->get_where('udt_AUM_Invitees',array('AuctionID'=>$AuctionId));
        return $q->row(); */
        
    }
    
    
    public function upload_image()
    {
        extract($this->input->post());
        $document=$_FILES['upload_file'];
        
        $bucket="hig-sam";
        if (!class_exists('S3')) { include_once APPPATH.'third_party/S3.php';
        }
        if (!defined('awsAccessKey')) { define('awsAccessKey', 'AKIAIDL7EVF4VORRXIBA');
        }
        if (!defined('awsSecretKey')) { define('awsSecretKey', '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');
        }
        $s3 = new S3(awsAccessKey, awsSecretKey);
        include_once APPPATH.'third_party/image_check.php';// getExtension Method 
            
            
        for($i=0;$i<count($document['name']);$i++){
            $ext=getExtension($document['name'][$i]);
            if($document['name'][$i]) {
                if($ext=='pdf' || $ext=='PDF') {
                    $nar=explode(".", $document['type'][$i]);
                    $type1=end($nar);
                    $file=rand(1, 999999).'_____'.$document['name'][$i];
                    $tmp=$document['tmp_name'][$i];
                    $filesize=$document['size'][$i];
                
                    $actual_image_name = 'TopMarx/'.$file;
                    $s3->putObjectFile($tmp, $bucket, $actual_image_name, S3::ACL_PUBLIC_READ);
                
                    $file_data = array(
                    'CoCode'=>C_COCODE,
                    'AuctionID'=>$AuctionId,
                    'LineNum'=>'1',
                    'AuctionSection'=>$type,
                    'FileName'=> $file,
                    'Title'=>$NameorTitleofdocumentattached,
                    'FileSizeKB'=>round($filesize/1024, 3),
                    'FileType'=>$type1,
                    'ToDisplay'=>$Documenttobedisplayinauctionprocess, 
                    'ToDisplayInvitee'=>$Documenttobedisplaytoinvitee, 
                    'DocumentType'=>$typeofdocument,
                    'AcceptNameFlg'=>$AcceptNameFlg,
                    'CustomTitle'=>$CustomTitle,
                    'UserID'=>$UserID, 
                    'CreatedDate'=>Date('Y-m-d H:i:s'),
                    'FileComment'=>$File_Comment[$i] 
                    );
             
                    $res=$this->db->insert('udt_AUM_Documents', $file_data);
                }
            }
        }
    }
    public function upload_image1()
    {
        /* $this->output->set_output(json_encode($_POST));
        die; */
        extract($this->input->post());
        $document=$_FILES['upload_file'];
        
        $version=explode(' ', $VesselVersion);
        $newversion=$version[1]+0.01;
        
        $bucket="hig-sam";
        if (!class_exists('S3')) { include_once APPPATH.'third_party/S3.php';
        }
        if (!defined('awsAccessKey')) { define('awsAccessKey', 'AKIAIDL7EVF4VORRXIBA');
        }
        if (!defined('awsSecretKey')) { define('awsSecretKey', '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');
        }
        $s3 = new S3(awsAccessKey, awsSecretKey);
        include_once APPPATH.'third_party/image_check.php';// getExtension Method 
            
            
        for($i=0;$i<count($document['name']);$i++){
            $ext=getExtension($document['name'][$i]);
            if($document['name'][$i]) {
                if($ext=='pdf' || $ext=='PDF') {
                    $nar=explode(".", $document['type'][$i]);
                    $type=end($nar);
                    $file=rand(1, 999999).$document['name'][$i];
                    $tmp=$document['tmp_name'][$i];
                    $filesize=$document['size'][$i];
                
                    $actual_image_name = 'TopMarx/'.$file;
                    $s3->putObjectFile($tmp, $bucket, $actual_image_name, S3::ACL_PUBLIC_READ);
                    $qids=explode("_", $ids);
        
                    $file_data = array(
                    'CoCode'=>C_COCODE,
                    'AuctionID'=>$AuctionId,
                    'ResponseID'=>$ResponseID,
                    'LineNum'=>'0',
                    'ResponseSection'=>'vessel',
                    'FileName'=> $file,
                    'DocumentType'=>$Document_Type[$i],
                    'DocumentTitle'=>$Document_Title[$i],
                    'FileType'=>$type,
                    'FileSizeKB'=>round($filesize/1024, 3),
                    'UserID'=>$UserID, 
                    'UserDate'=>Date('Y-m-d H:i:s'), 
                    'FlagBit'=>'1',
                    'CargoVersion'=>$newversion
                    );
                    $res=$this->db->insert('udt_AU_ResponseDocuments', $file_data);
                }
            }
        }
    }
    
    public function updateVesselData()
    {
        extract($this->input->post());
        //$this->output->set_output(json_encode($_POST));
        $data=array('CoCode'=>C_COCODE,
        'CommentAuction'=>$CommentAuction, 
        'CommentInvitee'=>$CommentInvitee, 
        'UserID'=>$UserID,
        'UserDate'=>date('Y-m-d H:i:s')
        );
        //print_r($data); die;
        $this->db->where('AuctionID', $AuctionId);
        $this->db->update('udt_AU_Vessel', $data); 
        
        $data1=array(
                    'DocumentType'=>$typeofdocument,
                    'Title'=>$NameorTitleofdocumentattached,
                    'ToDisplay'=>$Documenttobedisplayinauctionprocess,
                    'ToDisplayInvitee'=>$Documenttobedisplaytoinvitee
        );
        $this->db->where('AuctionID', $AuctionId);
        $this->db->where('AuctionSection', $type);
        $this->db->update('udt_AUM_Documents', $data1);
        
        
    }
    
    public function updateVesselData1()
    {
        $flag=0;
        extract($this->input->post());
        //$this->output->set_output(json_encode($_POST));
        if($selectvessel!='') {            
            $vessel=$selectvessel;
        }elseif($vesselnameis!='') {
            $vessel=$vesselnameis;
        }elseif($vesselname!='') {
            $vessel=$vesselname;
        }
        
        if($imonumberis!='') {            
            $imo=$imonumberis;
        }elseif($selectvesselbyimo!='') {
            $imo=$selectvesselbyimo;
        }elseif($imonumber!='') {
            $imo=$imonumber;
        }
        if($lightweight ) {
            $Dispalcement=$lightweight;
        }else{
            $Dispalcement=0;
        }
        
        if($cargodisplaycomments=='undefined') {
            $CommentInvitee='';
        }else{
            $CommentInvitee=$cargodisplaycomments;
        }
        
        if($responsedisplaycomments=='undefined') {
            $ResponseCommentInvitee='';
        }else{
            $ResponseCommentInvitee=$responsedisplaycomments;
        }
        
        if($vrrating=="null") {
            $vrrating=0;
        }
        if($issource=="null") {
            $issource='';
        }
        if($deadweight=='') {
            $deadweight=0;
        }
        if($loa=='') {
            $loa=0;
        }
        if($beam=='') {
            $beam=0;
        }
        if($draft=='') {
            $draft=0;
        }
        
        if($CountryID=='' || $CountryID=='undefined') {
            $CountryID=0;
        }
        
        if($StateID=='' || $StateID=='undefined') {
            $StateID=0;
        }
        
        $this->db->select('*');
        $this->db->from('udt_AU_ResponseVessel');
        $this->db->where('udt_AU_ResponseVessel.ResponseID', $ResponseID);
        $this->db->order_by('udt_AU_ResponseVessel.ResponseVesselID', 'DESC');
        $query=$this->db->get();
        $LatestVesselVersion=$query->row()->VesselVersion;
        
        $version=explode(' ', $LatestVesselVersion);
        $newversion=$version[1]+0.01;
        
        $data=array(
        'CoCode'=>C_COCODE,
        'VesselVersion'=>'Version '.$newversion,
        'ResponseID'=>$ResponseID,
        'AuctionID'=>$AuctionId,
        'RecordOwner'=>$RecordOwner,
        'SelectVesselBy'=>$vesselby,
        'VesselName'=>$vessel,
        'IMO'=>$imo,
        'VesselCurrentName'=>$vesselcurrentname,
        'VesselChangeNameDate'=>date('Y-m-d', strtotime($vesselchangedate)),
        'FirstLoadPortDate'=>date('Y-m-d', strtotime($lparrivaldate)),
        'LastDisPortDate'=>date('Y-m-d', strtotime($dparrivaldate)),
        'VesselConfirmFlg'=>$VesselConfirmFlg,
        'DisponentOwnerID'=>$entity_id,
        'Address1'=>$Address1,
        'Address2'=>$Address2,
        'Address3'=>$Address3,
        'Address4'=>$Address4,
        'CountryID'=>$CountryID,
        'StateID'=>$StateID,
        'LOA'=>$loa,
        'Beam'=>$beam,
        'Draft'=>$draft,
        'DeadWeight'=>$deadweight,
        'Displacement'=>$Dispalcement, 
        'Source'=>$vrsource, 
        'Rating'=>$vrrating, 
        'RatingDate'=>date('Y-m-d', strtotime($vratingdate)), 
        'SourceType'=>$issource, 
        'VettingSource'=>$sourceofvetting, 
        'Deficiency'=>$pscdeficiency, 
        'DeficiencyCompDate'=>date('Y-m-d', strtotime($pscdefcompdate)), 
        'DetentionFlag'=>$pscdetention, 
        'CommentAuction'=>$cargointernalcomments, 
        'CommentInvitee'=>$CommentInvitee, 
        'CommentByInvitee'=>$ResponseCommentInvitee, 
        'DetentionDate'=>date('Y-m-d', strtotime($pscdetentiondate)), 
        'DetentionLiftedFlag'=>$pscdetentionlifted, 
        'DetentionLiftedDate'=>date('Y-m-d', strtotime($pscdetentionlifteddate)), 
        'DetentionLiftExpectedDate'=>date('Y-m-d', strtotime($epscdetentionlifteddate)), 
        'UserID'=>$UserID,
        'RecordAddBy'=>$UserID,
        'UserDate'=>date('Y-m-d H:i:s')
        );
            
        $this->db->insert('udt_AU_ResponseVessel', $data); 
        
        if($InvFlg==1 && $ConfirmationFlg==1) {
            $data1=array(
            'ResponseStatus'=>'Inprogress',
            'ShipOwnerID'=>$entity_id,
            'conf1'=>'',
            'conf2'=>'',
            'conf3'=>'',
            'conf4'=>'',
            'UserDate'=>date('Y-m-d H:i:s'),
            'Status'=>2,
            'change_status'=>1
            );
        } else if($InvFlg==1 && $ConfirmationFlg==0) {
            $data1=array(
            'ResponseStatus'=>'Inprogress',
            'ShipOwnerID'=>$entity_id,
            'conf1'=>'on',
            'conf2'=>'on',
            'conf3'=>'on',
            'conf4'=>'on',
            'UserDate'=>date('Y-m-d H:i:s'),
            'Status'=>2,
            'change_status'=>1
            );
        } else {
            $data1=array(
            'ResponseStatus'=>'Inprogress',
            'ShipOwnerID'=>$entity_id,
            'UserDate'=>date('Y-m-d H:i:s'),
            'Status'=>2,
            'change_status'=>1
            );
        }
            
            
        $this->db->where('ResponseID', $ResponseID);
        $ret=$this->db->update('udt_AUM_Freight', $data1);
        
        $this->db->where('ResponseID', $ResponseID);
        $this->db->update('udt_AU_FreightResponseAssessment', $data1);

    }
    
    public function deleteInvitee()
    {
        $AuctionId=$this->input->post('auctionID');
        $invid=$this->input->post('id');
        $UserID=$this->input->post('UserID');
        
        $this->db->select('*');
        $this->db->from('udt_AUM_Invitees');
        $this->db->where('InvID', $invid);
        $this->db->where('AuctionID', $AuctionId);
        $qry=$this->db->get();
        $rw=$qry->row();
        
        $this->db->select('*');
        $this->db->from('udt_AU_Counter');
        $qry_counter=$this->db->get();
        $NewCounter=$qry_counter->row()->CounterNo+1;
        
        $this->db->update('udt_AU_Counter', array('CounterNo'=>$NewCounter)); 
        
        $data1=array(
        'CoCode'=>$rw->CoCode,
        'AuctionID'=>$AuctionId,
        'Company'=>$rw->Company,
        'EntityID'=>$rw->EntityID,
        'UserMasterID'=>$rw->UserMasterID,
        'InvPriorityStatus'=>$rw->InvPriorityStatus,
        'InviteeRole'=>$rw->InviteeRole,
        'QuoteLimitFlag'=>$rw->QuoteLimitFlag,
        'QuoteLimitValue'=>$rw->QuoteLimitValue,
        'Since'=>'',
        'AdverseComments'=>'',
        'Comments'=>'',                                        
        'RowStatus'=>'3',
        'RowCounter'=>$NewCounter,
        'UserID'=>$UserID,
        'UserDate'=>date('Y-m-d H:i:s')            
        );
        $this->db->insert('udt_AUM_Invitees_H', $data1);        
    
        
        $this->db->where('AuctionID', $AuctionId);
        $this->db->where('UserList', $rw->UserMasterID);
        $this->db->delete('udt_AU_BusinessProcessAuctionWise');
        
        $this->db->where('AuctionID', $AuctionId);
        $this->db->where('InvID', $invid);
        return $this->db->delete('udt_AUM_Invitees');
    }
    
    public function getVessel()
    {
        extract($this->input->post());
        
        $this->db->select('*');
        $this->db->from('udt_AU_Vessel');
        $this->db->where('AuctionID', $AuctionId);
        $query= $this->db->get();
        $result=$query->row();
        return $result;
    }
    
    public function saveVessel($oldData,$newData)
    {
        $AuctionID=$this->input->post('AuctionId');
        $UserId=$this->input->post('UserID');
        
        $this->db->select('udt_AUM_MESSAGE_MASTER.MessageID');
        $this->db->from('udt_AUM_MESSAGE_MASTER');
        $this->db->where('udt_AUM_MESSAGE_MASTER.ForUserID', $UserId);
        $this->db->where('udt_AUM_MESSAGE_MASTER.MessageType', 'sys_msg');
        $this->db->where('udt_AUM_MESSAGE_MASTER.Events', 'edit_update');
        $query=$this->db->get();
        $msgData=$query->row();
        
        $section='';
        $message='';
        if($oldData->CommentAuction != $newData->CommentAuction) {
            $section='Vessel';
            $message .='<br>Old Cargo owner Comment : '.$oldData->CommentAuction.' New Cargo owner Comment : '.$newData->CommentAuction;
        }
        
        if($oldData->CommentInvitee != $newData->CommentInvitee) {
            $section='Vessel';
            $message .='<br>Old Invitee Comment : '.$oldData->CommentInvitee.' New Invitee Comment : '.$newData->CommentInvitee;
        }
        if($section=='Vessel') {
            if($msgData) {
                $vesseldata=array( 
                'CoCode'=>'marx',
                'AuctionID'=>$AuctionID,
                'Event'=>'Edit & Update',
                'Page'=>'Cargo Set Up',
                'Section'=>'Vessel',
                'subSection'=>$section,
                'StatusFlag'=>'1',
                'MessageDetail'=>$message,
                'MessageMasterID'=>$msgData->MessageID,
                'UserID'=>$UserId,
                'FromUserID'=>$UserId,
                'UserDate'=>date('Y-m-d H:i:s')
                );
                     
                $this->db->insert('udt_AU_Messsage_Details', $vesseldata);
                $msg_data=array(
                'MessageFlag'=>'1',
                'MsgDate'=>date('Y-m-d H:i:s')
                );
                                
                $this->db->where('AuctionID', $AuctionID);
                $this->db->update('udt_AU_Auctions', $msg_data);
            }                        
        } 
    }
    
    public function getInvitee_1()
    {
        $AuctionId=$this->input->post('AuctionId');
        $this->db->select('InvPriorityStatus,AdverseComments,Comments');
        $this->db->from('udt_AUM_Invitees');
        $this->db->where('AuctionID', $AuctionId);
        $query=$this->db->get();
        return $query->row();
    }
    
    public function saveInvitee_1($oldData,$newData)
    {
        $AuctionId=$this->input->post('AuctionId');
        $UserId=$this->input->post('UserID');
        $this->db->select('udt_AUM_MESSAGE_MASTER.MessageID');
        $this->db->from('udt_AUM_MESSAGE_MASTER');
        $this->db->where('udt_AUM_MESSAGE_MASTER.ForUserID', $UserId);
        $this->db->where('udt_AUM_MESSAGE_MASTER.MessageType', 'sys_msg');
        $this->db->where('udt_AUM_MESSAGE_MASTER.Events', 'edit_update');
        $query=$this->db->get();
        $msgData=$query->row();
        
        $section='';
        $message='';
        if($oldData->AdverseComments != 'undefined' ||  $newData->AdverseComments != 'undefined') {
            if($oldData->AdverseComments != $newData->AdverseComments) {
                $section='Invitee';
                $message .='Old Cargo owner Comment : '.$oldData->AdverseComments.' New Cargo owner Comment : '.$newData->AdverseComments;
            }
        }
        if($oldData->Comments != 'undefined' || $newData->Comments != 'undefined') {
            if($oldData->Comments != $newData->Comments) {
                $section='Invitee';
                $message .='Old Invitee Comment : '.$oldData->Comments.' New Invitee Comment : '.$newData->Comments;
            }
        }
        if($section=='Invitee') {
            if($msgData) {
                $inviteeData=array( 
                'CoCode'=>'marx',
                'AuctionID'=>$AuctionId,
                'Event'=>'Edit & Update',
                'Page'=>'Cargo Set Up',
                'Section'=>'Invitee',
                'subSection'=>$section,
                'StatusFlag'=>'1',
                'MessageDetail'=>$message,
                'MessageMasterID'=>$msgData->MessageID,
                'UserID'=>$UserId,
                'FromUserID'=>$UserId,
                'UserDate'=>date('Y-m-d H:i:s')
                );
                         
                $this->db->insert('udt_AU_Messsage_Details', $inviteeData);                    
                $msg_data=array(
                'MessageFlag'=>'1',
                'MsgDate'=>date('Y-m-d H:i:s')
                );
                                
                $this->db->where('AuctionID', $AuctionId);
                $this->db->update('udt_AU_Auctions', $msg_data);
            }
        }  
    } 
    
    public function getInvitee()
    {
        $AuctionId=$this->input->get('AuctionId');
        $this->db->select('InvPriorityStatus,AdverseComments,Comments');
        $this->db->from('udt_AUM_Invitees');
        $this->db->where('AuctionID', $AuctionId);
        $this->db->order_by('InvPriorityStatus', 'Desc');
        $query=$this->db->get();
        return $query->row();
    }
    
    public function saveInvitee($oldData,$newData)
    {
        $AuctionID=$this->input->get('AuctionId');
        $UserId=$this->input->get('UserID');
        
        $this->db->select('udt_AUM_MESSAGE_MASTER.MessageID');
        $this->db->from('udt_AUM_MESSAGE_MASTER');
        $this->db->where('udt_AUM_MESSAGE_MASTER.ForUserID', $UserId);
        $this->db->where('udt_AUM_MESSAGE_MASTER.MessageType', 'sys_msg');
        $this->db->where('udt_AUM_MESSAGE_MASTER.Events', 'edit_update');
        $query=$this->db->get();
        $msgData=$query->row();
        
        $section='';
        $message='';
        if($oldData->InvPriorityStatus != '') {
            if($oldData->InvPriorityStatus != $newData->InvPriorityStatus) {
                $section='Invitee';
                if($oldData->InvPriorityStatus=='P1') {
                    $oldInvPriorityStatus='Invitee Priority 1';
                }else if($oldData->InvPriorityStatus=='P2') {
                    $oldInvPriorityStatus='Invitee Priority 2';
                }else if($oldData->InvPriorityStatus=='P3') {
                    $oldInvPriorityStatus='Invitee Priority 3';
                }else if($oldData->InvPriorityStatus=='P0') {
                    $oldInvPriorityStatus='Global (priority 1,2,3)';
                }
                if($newData->InvPriorityStatus=='P1') {
                    $newInvPriorityStatus='Invitee Priority 1';
                }else if($newData->InvPriorityStatus=='P2') {
                    $newInvPriorityStatus='Invitee Priority 2';
                }else if($newData->InvPriorityStatus=='P3') {
                    $newInvPriorityStatus='Invitee Priority 3';
                }else if($newData->InvPriorityStatus=='P0') {
                    $newInvPriorityStatus='Global (priority 1,2,3)';
                }
                $message .='<br>Old Invitee Priority : '.$oldInvPriorityStatus.' New Invitee Priority : '.$newInvPriorityStatus;
            }
        }
        if($section=='Invitee') {
            if($msgData) {
                $inviteeData=array( 
                'CoCode'=>'marx',
                'AuctionID'=>$AuctionID,
                'Event'=>'Edit & Update',
                'Page'=>'Cargo Set Up',
                'Section'=>'Invitee',
                'subSection'=>$section,
                'StatusFlag'=>'1',
                'MessageDetail'=>$message,
                'MessageMasterID'=>$msgData->MessageID,
                'UserID'=>$UserId,
                'FromUserID'=>$UserId,
                'UserDate'=>date('Y-m-d H:i:s')
                );
                //print_R($vesseldata); die;         
                $this->db->insert('udt_AU_Messsage_Details', $inviteeData);    
                $msg_data=array(
                'MessageFlag'=>'1',
                'MsgDate'=>date('Y-m-d H:i:s')
                );
                                
                $this->db->where('AuctionID', $AuctionID);
                $this->db->update('udt_AU_Auctions', $msg_data);    
            }
        } 
    }
    
    public function getOpenClosedData()
    {
        extract($this->input->post());
        $sd=date('Y-m-d');
        $this->db->select('auctionStatus,count(auctionStatus) as t');
        $this->db->from('udt_AU_Auctions');    
        $this->db->join('udt_AU_Cargo', 'udt_AU_Auctions.AuctionID=udt_AU_Cargo.AuctionID', 'left');    
        $this->db->where('udt_AU_Auctions.ActiveFlag', 1);
        if($loadport) {
            $this->db->where('udt_AU_Cargo.LoadPort', $loadport);    
        }
        if($laycan_from) {
            $this->db->where('udt_AU_Cargo.LpLaycanStartDate >= ', date('Y-m-d', strtotime($laycan_from)));    
        } else {
            $this->db->where('udt_AU_Cargo.LpLaycanStartDate >=', $sd);
        }
        if($laycan_to) {
            $this->db->where('udt_AU_Cargo.LpLaycanEndDate <= ', date('Y-m-d', strtotime($laycan_to)));    
        } else {
            $this->db->where('udt_AU_Cargo.LpLaycanEndDate <= ', date('Y-m-d', strtotime("+5 months")));    
        }
        if($disport) {
            $this->db->where('udt_AU_Cargo.DisPort', $disport);    
        }
        if($cargo) {
            $this->db->where('udt_AU_Cargo.SelectFrom', $cargo);    
        }
        if($est_frt_mt_from) {
            $this->db->where('udt_AU_Cargo.Estimate_mt >= ', $est_frt_mt_from);    
        }
        if($est_frt_mt_to) {
            $this->db->where('udt_AU_Cargo.Estimate_mt <= ', $est_frt_mt_to);    
        }
        if($index_frt_mt_from) {
            $this->db->where('udt_AU_Cargo.Estimate_Index_mt >= ', $index_frt_mt_from);    
        }
        if($index_frt_mt_to) {
            $this->db->where('udt_AU_Cargo.Estimate_Index_mt <= ', $index_frt_mt_to);    
        }
        if($Ower) {
            $this->db->where('udt_AU_Auctions.OwnerEntityID', $Ower);    
        }
        $this->db->group_by('udt_AU_Auctions.auctionStatus');
        $query=$this->db->get();
        return $query->result(); 
    }
    public function getOpenGraphData()
    {
        extract($this->input->post());
        $sd=date('Y-m-d');
        $this->db->select('auctionExtendedStatus,count(auctionExtendedStatus) as t');
        $this->db->from('udt_AU_Auctions');
        $this->db->join('udt_AU_Cargo', 'udt_AU_Auctions.AuctionID=udt_AU_Cargo.AuctionID', 'left');
        $this->db->where('udt_AU_Auctions.auctionStatus', 'C');
        $this->db->where('udt_AU_Auctions.ActiveFlag', 1);
        if($loadport) {
            $this->db->where('udt_AU_Cargo.LoadPort', $loadport);    
        }
        if($laycan_from) {
            $this->db->where('udt_AU_Cargo.LpLaycanStartDate >= ', date('Y-m-d', strtotime($laycan_from)));    
        } else {
            $this->db->where('udt_AU_Cargo.LpLaycanStartDate >=', $sd);
        }
        if($laycan_to) {
            $this->db->where('udt_AU_Cargo.LpLaycanEndDate <= ', date('Y-m-d', strtotime($laycan_to)));    
        } else {
            $this->db->where('udt_AU_Cargo.LpLaycanEndDate <= ', date('Y-m-d', strtotime("+5 months")));    
        }
        if($disport) {
            $this->db->where('udt_AU_Cargo.DisPort', $disport);    
        }
        if($cargo) {
            $this->db->where('udt_AU_Cargo.SelectFrom', $cargo);    
        }
        if($est_frt_mt_from) {
            $this->db->where('udt_AU_Cargo.Estimate_mt >= ', $est_frt_mt_from);    
        }
        if($est_frt_mt_to) {
            $this->db->where('udt_AU_Cargo.Estimate_mt <= ', $est_frt_mt_to);    
        }
        if($index_frt_mt_from) {
            $this->db->where('udt_AU_Cargo.Estimate_Index_mt >= ', $index_frt_mt_from);    
        }
        if($index_frt_mt_to) {
            $this->db->where('udt_AU_Cargo.Estimate_Index_mt <= ', $index_frt_mt_to);    
        }
        if($Ower) {
            $this->db->where('udt_AU_Auctions.OwnerEntityID', $Ower);    
        }
        $this->db->group_by('udt_AU_Auctions.auctionExtendedStatus');
        $query=$this->db->get();
        return $query->result();
        
    }
    
    public function getResponseVesselLatestOpen()
    {
        extract($this->input->post());
        $this->db->select('udt_AU_ResponseVessel.*, udt_EntityMaster.EntityName, udt_CountryMaster.Code as C_Code, udt_CountryMaster.Description as C_Description, udt_StateMaster.Code as S_Code, udt_StateMaster.Description as S_Description');
        $this->db->from('udt_AU_ResponseVessel');
        $this->db->join('udt_EntityMaster', 'udt_EntityMaster.ID=udt_AU_ResponseVessel.DisponentOwnerID', 'left');
        $this->db->join('udt_CountryMaster', 'udt_CountryMaster.ID=udt_AU_ResponseVessel.CountryID', 'left');
        $this->db->join('udt_StateMaster', 'udt_StateMaster.ID=udt_AU_ResponseVessel.StateID', 'left');
        $this->db->where('udt_AU_ResponseVessel.ResponseID', $ResponseID);
        $this->db->order_by('udt_AU_ResponseVessel.ResponseVesselID', 'Desc');
        $query=$this->db->get();
        return $query->row();
    }
    
    public function updateResponseVesselContentChange($olddata,$newdata)
    {
        $ResponseID=$this->input->post('ResponseID');
    
        $html='';
        $section='';
        $totalhtml='';
        if($olddata->SelectVesselBy != $newdata->SelectVesselBy) {
            if($olddata->SelectVesselBy == 1) {
                $OldSelectVesselBy='Vessel name incl ex_name';
            }else if($olddata->SelectVesselBy == 2) {
                $OldSelectVesselBy='IMO number';
            }else if($olddata->SelectVesselBy == 3) {
                $OldSelectVesselBy='Vessel not found';
            }else{
                $OldSelectVesselBy='';
            }
            if($newdata->SelectVesselBy == 1) {
                $NewSelectVesselBy='Vessel name incl ex_name';
            }else if($newdata->SelectVesselBy == 2) {
                $NewSelectVesselBy='IMO number';
            }else if($newdata->SelectVesselBy == 3) {
                $NewSelectVesselBy='Vessel not found';
            }else{
                $NewSelectVesselBy='';
            }
            $section='<br><br><B>Vessel Selection</B><br>';
            $html .='<br> Old vessel by : '.$OldSelectVesselBy.' <span class="diff">||</span> New vessel by : '.$NewSelectVesselBy;
        }
        
        if($olddata->VesselName != $newdata->VesselName) {
            $section='<br><br><B>Vessel Selection</B><br>';
            $html .='<br> Old vessel name :'.$olddata->VesselName.' <span class="diff">||</span> New vessel name : '.$newdata->VesselName;
        }
        
        if($olddata->IMO != $newdata->IMO) {
            $section='<br><br><B>Vessel Selection</B><br>';
            $html .='<br>Old IMO number : '.$olddata->IMO.' <span class="diff">||</span> New IMO number : '.$newdata->IMO;
        }
        
        if($olddata->VesselCurrentName !='NULL') {
            if($olddata->VesselCurrentName != $newdata->VesselCurrentName) {
                $section='<br><br><B>Vessel Selection</B><br>';
                $html .='<br>Old Vessel current name : '.$olddata->VesselCurrentName.' <span class="diff">||</span> New Vessel current name : '.$newdata->VesselCurrentName;
                $html .='<br>Old Vessel current name date : '.date('d-m-Y', strtotime($olddata->VesselChangeNameDate)).' <span class="diff">||</span> New Vessel current date : '.date('d-m-Y', strtotime($newdata->VesselChangeNameDate));
            }
        }
        
        $OldFirstLoadPortDate=date('d-m-Y', strtotime($olddata->FirstLoadPortDate));
        $NewFirstLoadPortDate=date('d-m-Y', strtotime($newdata->FirstLoadPortDate));
        if($OldFirstLoadPortDate == '01-01-1970') {
            $OldFirstLoadPortDate='';
        }
        
        if($NewFirstLoadPortDate == '01-01-1970') {
            $NewFirstLoadPortDate='';
        }
        
        if($OldFirstLoadPortDate != $NewFirstLoadPortDate ) {
            $section='<br><br><B>Vessel Selection</B><br>';
            $html .='<br>Old expected first loadport arrival date : '.$OldFirstLoadPortDate.' <span class="diff">||</span> New expected first loadport arrival date : '.$NewFirstLoadPortDate;
        }
        
        $OldLastDisPortDate=date('d-m-Y', strtotime($olddata->LastDisPortDate));
        $NewLastDisPortDate=date('d-m-Y', strtotime($newdata->LastDisPortDate));
        if($OldLastDisPortDate == '01-01-1970') {
            $OldLastDisPortDate='';
        }
        
        if($NewLastDisPortDate == '01-01-1970') {
            $NewLastDisPortDate='';
        }
        
        if($OldLastDisPortDate != $NewLastDisPortDate ) {
            $section='<br><br><B>Vessel Selection</B><br>';
            $html .='<br>Old expected first disport arrival date : '.$OldLastDisPortDate.' <span class="diff">||</span> New expected first disport arrival date : '.$NewLastDisPortDate;
        
        }
        
        $totalhtml .=$section;
        $totalhtml .=$html;
        $section='';
        $html='';
        
        if($olddata->DisponentOwnerID != $newdata->DisponentOwnerID) {
            $section='<br><br><B>Ship (disponent) owner</B><br>';
            $html .='<br>Old associated entity name : '.$olddata->EntityName.' <span class="diff">||</span> New associated entity name : '.$newdata->EntityName;
        }
        
        if($olddata->Address1 != $newdata->Address1) {
            $section='<br><br><B>Ship (disponent) owner</B><br>';
            $html .='<br>Old address1 : '.$olddata->Address1.' <span class="diff">||</span> New address1 : '.$newdata->Address1;
        }
        
        if($olddata->Address2 != $newdata->Address2) {
            $section='<br><br><B>Ship (disponent) owner</B><br>';
            $html .='<br>Old address2 : '.$olddata->Address2.' <span class="diff">||</span> New address2 : '.$newdata->Address2;
        }
        
        if($olddata->Address3 != $newdata->Address3) {
            $section='<br><br><B>Ship (disponent) owner</B><br>';
            $html .='<br>Old address3 : '.$olddata->Address3.' <span class="diff">||</span> New address3 : '.$newdata->Address3;
        }
        
        if($olddata->Address4 != $newdata->Address4) {
            $section='<br><br><B>Ship (disponent) owner</B><br>';
            $html .='<br>Old address4 : '.$olddata->Address4.' <span class="diff">||</span> New address4 : '.$newdata->Address4;
        }
        
        if($olddata->CountryID != $newdata->CountryID) {
            $section='<br><br><B>Ship (disponent) owner</B><br>';
            $html .='<br>Old country name : '.$olddata->C_Description.' <span class="diff">||</span> New country name : '.$newdata->C_Description;
        }
        
        if($olddata->StateID != $newdata->StateID) {
            $section='<br><br><B>Ship (disponent) owner</B><br>';
            $html .='<br>Old state name : '.$olddata->S_Description.' <span class="diff">||</span> New state name : '.$newdata->S_Description;
        }
        
        $totalhtml .=$section;
        $totalhtml .=$html;
        $section='';
        $html='';
        
        if($olddata->LOA != $newdata->LOA) {
            $section='<br><br><B>Vessel Particulars</B><br>';
            $html .='<br>Old length overall (LOA) (m) : '.$olddata->LOA.' <span class="diff">||</span> New length overall (LOA) (m) : '.$newdata->LOA;
        }
        
        if($olddata->Beam != $newdata->Beam) {
            $section='<br><br><B>Vessel Particulars</B><br>';
            $html .='<br>Old beam (m) : '.number_format($olddata->Beam).' <span class="diff">||</span> New beam (m) : '.number_format($newdata->Beam);
        }
        
        if($olddata->Draft != $newdata->Draft) {
            $section='<br><br><B>Vessel Particulars</B><br>';
            $html .='<br>Old draft (m) : '.$olddata->Draft.' <span class="diff">||</span> New draft (m) : '.$newdata->Draft;
        }
        
        if($olddata->DeadWeight != $newdata->DeadWeight) {
            $section='<br><br><B>Vessel Particulars</B><br>';
            $html .='<br>Old deadweight (mt) : '.number_format($olddata->DeadWeight).' <span class="diff">||</span> New deadweight (mt) : '.number_format($newdata->DeadWeight);
        }
        
        if($olddata->Displacement != $newdata->Displacement) {
            $section='<br><br><B>Vessel Particulars</B><br>';
            $html .='<br>Old lightweight displacement (mt) : '.number_format($olddata->Displacement).' <span class="diff">||</span> New lightweight displacement (mt) : '.number_format($newdata->Displacement);
        }
        
        $totalhtml .=$section;
        $totalhtml .=$html;
        $section='';
        $html='';
        
        if($olddata->Source != $newdata->Source) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old vetting risk source : '.$olddata->Source.' <span class="diff">||</span> New vetting risk source : '.$newdata->Source;
        }
        
        if($olddata->Rating != $newdata->Rating) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old vetting risk rating : '.$olddata->Rating.' <span class="diff">||</span> New vetting risk rating : '.$newdata->Rating;
        }
        $OldRatingDate=date('d-m-Y', strtotime($olddata->RatingDate));
        $NewRatingDate=date('d-m-Y', strtotime($newdata->RatingDate));
        if($OldRatingDate == '01-01-1970') {
            $OldRatingDate='';
        }
        if($NewRatingDate == '01-01-1970') {
            $NewRatingDate='';
        }    
        if($OldRatingDate != $NewRatingDate) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old vetting rating date : '.$OldRatingDate.' <span class="diff">||</span> New vetting rating date : '.$NewRatingDate;
        
        }
        
        if($olddata->SourceType != $newdata->SourceType) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old source type : '.$olddata->SourceType.' <span class="diff">||</span> New source type : '.$newdata->SourceType;
        }
        
        if($olddata->VettingSource != 'NULL') {
            if($olddata->VettingSource != $newdata->VettingSource) {
                $section='<br><br><B>Vessel Risk</B><br>';
                $html .='<br>Old source of vetting  : '.$olddata->VettingSource.' <span class="diff">||</span> New source of vetting  : '.$newdata->VettingSource;
            }
        }
        
        if($olddata->Deficiency != $newdata->Deficiency ) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old PSC deficiency existing : '.$olddata->Deficiency.' <span class="diff">||</span> New PSC deficiency existing : '.$newdata->Deficiency;
        }
        $OldDeficiencyCompDate=date('d-m-Y', strtotime($olddata->DeficiencyCompDate));
        $NewDeficiencyCompDate=date('d-m-Y', strtotime($newdata->DeficiencyCompDate));
        if($OldDeficiencyCompDate == '01-01-1970') {
            $OldDeficiencyCompDate='';
        }
        if($NewDeficiencyCompDate == '01-01-1970') {
            $NewDeficiencyCompDate='';
        }        
        if($OldDeficiencyCompDate != $NewDeficiencyCompDate ) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old PSC deficiency existing : '.$OldDeficiencyCompDate.' <span class="diff">||</span> New PSC deficiency existing : '.$NewDeficiencyCompDate;
        }
        
        if($olddata->DetentionFlag != $newdata->DetentionFlag ) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old PSC detention : '.$olddata->DetentionFlag.' <span class="diff">||</span> New PSC detention : '.$newdata->DetentionFlag;
        }
        $OldDetentionDate=date('d-m-Y', strtotime($olddata->DetentionDate));
        $NewDetentionDate=date('d-m-Y', strtotime($newdata->DetentionDate));
        if($OldDetentionDate == '01-01-1970') {
            $OldDetentionDate='';
        }
        if($NewDetentionDate == '01-01-1970') {
            $NewDetentionDate='';
        }    
        if($OldDetentionDate != $NewDetentionDate ) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old PSC detention date : '.$OldDetentionDate.' <span class="diff">||</span> New PSC detention date : '.$NewDetentionDate;
        }
        
        if($olddata->DetentionLiftedFlag != 'NULL') {
            if($olddata->DetentionLiftedFlag != $newdata->DetentionLiftedFlag) {
                $section='<br><br><B>Vessel Risk</B><br>';
                $html .='<br>Old PSC detention lifted  : '.$olddata->DetentionLiftedFlag.' <span class="diff">||</span> New PSC detention lifted  : '.$newdata->DetentionLiftedFlag;
            }
        }
        $OldDetentionLiftedDate=date('d-m-Y', strtotime($olddata->DetentionLiftedDate));
        $NewDetentionLiftedDate=date('d-m-Y', strtotime($newdata->DetentionLiftedDate));
        if($OldDetentionLiftedDate == '01-01-1970') {
            $OldDetentionLiftedDate='';
        }
        if($NewDetentionLiftedDate == '01-01-1970') {
            $NewDetentionLiftedDate='';
        }    
        if($OldDetentionLiftedDate != $NewDetentionLiftedDate ) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old PSC detention lifted on date : '.$OldDetentionLiftedDate.' <span class="diff">||</span> New PSC detention lifted on date : '.$NewDetentionLiftedDate;
        }
        $OldDetentionLiftExpectedDate=date('d-m-Y', strtotime($olddata->DetentionLiftExpectedDate));
        $NewDetentionLiftExpectedDate=date('d-m-Y', strtotime($newdata->DetentionLiftExpectedDate));
        if($OldDetentionLiftExpectedDate == '01-01-1970') {
            $OldDetentionLiftExpectedDate='';
        }
        if($NewDetentionLiftExpectedDate == '01-01-1970') {
            $NewDetentionLiftExpectedDate='';
        }        
        if($OldDetentionLiftExpectedDate != $NewDetentionLiftExpectedDate ) {
            $section='<br><br><B>Vessel Risk</B><br>';
            $html .='<br>Old Expected PSC detention lifted on date : '.$OldDetentionLiftExpectedDate.' <span class="diff">||</span> New Expected PSC detention lifted on date : '.$NewDetentionLiftExpectedDate;
        }
        
        $totalhtml .=$section;
        $totalhtml .=$html;
        $section='';
        $html='';
        
        $this->db->select('udt_AU_ResponseBrokerUsers_H.*,udt_UserMaster.FirstName,udt_UserMaster.LastName');
        $this->db->from('udt_AU_ResponseBrokerUsers_H');
        $this->db->join('udt_UserMaster', 'udt_UserMaster.ID=udt_AU_ResponseBrokerUsers_H.SigningUserID');
        $this->db->where('ResponseID', $ResponseID);
        $this->db->where('ResponseVesselID', $olddata->ResponseVesselID);
        $rbu_qry=$this->db->get();
        $old_rslt=$rbu_qry->result();
        
        $this->db->select('udt_AU_ResponseBrokerUsers.*,udt_UserMaster.FirstName,udt_UserMaster.LastName');
        $this->db->from('udt_AU_ResponseBrokerUsers');
        $this->db->join('udt_UserMaster', 'udt_UserMaster.ID=udt_AU_ResponseBrokerUsers.SigningUserID');
        $this->db->where('ResponseID', $ResponseID);
        $rbu_qry1=$this->db->get();
        $new_rslt=$rbu_qry1->result();
        
        foreach($new_rslt as $nrw){
            if(count($old_rslt) <= 0) {
                $section='<br><br><B>Permission</B><br>';
                if($nrw->Status==1) {
                    $Status='Active';
                } else {
                    $Status='Inactive';
                }
                if($nrw->BrokerSigningType==1) {
                    $BrokerSigningType='Fixture Note';
                } else if($nrw->BrokerSigningType==2) {
                    $BrokerSigningType='Charter Party';
                }
                $html .='<br>'.$BrokerSigningType.' ('.$nrw->FirstName.' '.$nrw->LastName.') old status :  <span class="diff">||</span> '.$BrokerSigningType.' '.$nrw->FirstName.' '.$nrw->LastName.' new status : '.$Status;
            } else {
                foreach($old_rslt as $orw){
                    if($nrw->UserID==$orw->UserID && $nrw->BrokerSigningType==$orw->BrokerSigningType && $nrw->Status !=$orw->Status) {
                        $section='<br><br><B>Permission</B><br>';
                        if($nrw->Status==1) {
                            $NewStatus='Active';
                        } else {
                            $NewStatus='Deactive';
                        }
                        if($orw->Status==1) {
                            $OldStatus='Active';
                        } else {
                            $OldStatus='Deactive';
                        }
                        if($nrw->BrokerSigningType==1) {
                            $BrokerSigningType='Fixture Note';
                        } else if($nrw->BrokerSigningType==2) {
                            $BrokerSigningType='Charter Party';
                        }
                        $html .='<br> '.$BrokerSigningType.' ('.$orw->FirstName.' '.$orw->LastName.') old status : '.$OldStatus.'  <span class="diff">||</span> '.$BrokerSigningType.' '.$nrw->FirstName.' '.$nrw->LastName.' new status : '.$NewStatus;
                    }
                }
            }
            
            $data=array(
            'ResponseID'=>$ResponseID,
            'ResponseVesselID'=>$newdata->ResponseVesselID,
            'BSU_ID'=>$nrw->BSU_ID,
            'BrokerSigningType'=>$nrw->BrokerSigningType,
            'SigningUserEntity'=>$nrw->SigningUserEntity,
            'SigningUserID'=>$nrw->SigningUserID,
            'Status'=>$nrw->Status,
            'UserID'=>$nrw->UserID,
            'CreatedDate'=>date('Y-m-d H:i:s')
            );
            $this->db->insert('udt_AU_ResponseBrokerUsers_H', $data);
            
        }
        
        $totalhtml .=$section;
        $totalhtml .=$html;
        $section='';
        $html='';
        
        $totalhtml=substr($totalhtml, 8);
        
        $this->db->where('udt_AU_ResponseVessel.ResponseVesselID', $newdata->ResponseVesselID);
        return $this->db->update('udt_AU_ResponseVessel', array('ContentChange'=>$totalhtml));
    }
    
    
    
}


