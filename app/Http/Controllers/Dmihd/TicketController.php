<?php

namespace App\Http\Controllers\Dmihd;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\Dmihd\DmihdFile;
use App\Models\Dmihd\DmihdFileTicket;
use App\Models\Dmihd\DmihdParticipant;
use App\Models\Dmihd\DmihdStatu;
use App\Models\Dmihd\DmihdTicket;
use App\Models\Dmihd\DmihdTicketStatuse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function sendTickets()
    {
        $user = new LoginController();
        $usuario = $user->check();
        $res = DmihdTicket::with(['prioritySubArea.priority', 'ticketStatuse', 'status',
            'participants' => function ($query) {
                $query->where("hierarchy", "primary");
            }, 'participants.user'])
            ->where('user_created_id', $usuario->original[0]->personal_intelisis_id)->orderByDesc('created_at')->get();
        return response()->json($res, 200);
    }
    public function requestTickets()
    {
        $user = new LoginController();
        $usuario = $user->check();
        $res = DmihdTicket::select('dmihd_tickets.*')
            ->with(['prioritySubArea.priority', 'ticketStatuse', 'status', 'user'])
            ->join('dmihd_participants', 'dmihd_tickets.id', '=', 'dmihd_participants.dmihd_ticket_id')
            ->where('dmihd_participants.user_id', $usuario->original[0]->personal_intelisis_id)
            ->orderByDesc('created_at')->get();
        return response()->json($res, 200);
    }
    public function filterSendTicket(Request $request)
    {
        $user = new LoginController();
        $usuario = $user->check();

        $res = DmihdTicket::select('dmihd_tickets.*')
            ->with(['prioritySubArea.priority', 'ticketStatuse', 'status',
                'participants' => function ($query) {
                    $query->where("hierarchy", "primary");
                }, 'participants.user'])
            ->join('dmihd_participants', 'dmihd_tickets.id', '=',
                'dmihd_participants.dmihd_ticket_id');
        if ($request->status != "") {
            $status = DmihdStatu::where('status', $request->status)->first();
            $res = $res->join('dmihd_status', 'dmihd_tickets.dmihd_status_id', '=',
                'dmihd_status.id')->where('dmihd_status.status', $request->status);
        }
        if ($request->fecha != null && $request->fecha != "") {
            $fechaInicio = $request->fecha[0];
            $fechaFin = $request->fecha[1];
            $res = $res->whereRaw('dmihd_tickets.created_at BETWEEN "' . $fechaInicio . '" AND "' . $fechaFin . '"');
        }
        $res = $res->where('dmihd_tickets.subject', 'like', '%' . $request->asunto . '%')
            ->where('dmihd_participants.user_id', 'like', '%' . $request->addressee_secundary . '%')
            ->where("dmihd_participants.hierarchy", "primary")
            ->where('dmihd_tickets.user_created_id', $usuario->original[0]->personal_intelisis_id)
            ->orderByDesc('dmihd_tickets.created_at')->get();
        return response()->json($res, 200);
    }
    public function filterRerquestTicket(Request $request)
    {
        $user = new LoginController();
        $usuario = $user->check();

        $res = DmihdTicket::select('dmihd_tickets.*')
            ->with(['prioritySubArea.priority', 'ticketStatuse', 'status', 'user'])
            ->join('dmihd_participants', 'dmihd_tickets.id', '=', 'dmihd_participants.dmihd_ticket_id');
      
        if ($request->status != "") {
            $status = DmihdStatu::where('status', $request->status)->first();
            $res = $res->join('dmihd_status', 'dmihd_tickets.dmihd_status_id', '=',
                'dmihd_status.id')->where('dmihd_status.status', $request->status);
        }
        if ($request->fecha != null && $request->fecha != "") {
            $fechaInicio = $request->fecha[0];
            $fechaFin = $request->fecha[1];
            $res = $res->whereRaw('dmihd_tickets.created_at BETWEEN "' . $fechaInicio . '" AND "' . $fechaFin . '"');
        }
        $res = $res->where('dmihd_tickets.subject', 'like', '%' . $request->asunto . '%')
            ->where('dmihd_participants.user_id', $usuario->original[0]->personal_intelisis_id )
            ->where("dmihd_participants.hierarchy", "primary")
            ->where('dmihd_tickets.user_created_id', 'like', '%' . $request->addressee_secundary . '%')
            ->orderByDesc('dmihd_tickets.created_at')->get();
        return response()->json($res, 200);
    }
    public function store(Request $request)
    {
        try {
            $rules = [
                'addressee' => 'required',
                'area' => 'required',
                'description' => 'required',
                'sub_area' => 'required',
                'affair' => 'required',
            ];

            $messages = [
                'addressee.required' => 'El campo destinatario es requerido',
                'area.required' => 'El campo área es requerido',
                'description.required' => 'El campo  descripción es requerido',
                'sub_area.required' => 'El campo  sub-área es requerido',
                'affair.required' => 'El campo  asunto es requerido',
            ];
            $this->validate($request, $rules, $messages);
            $user = new LoginController();
            $usuario = $user->check();
            $status = DmihdStatu::where('status', 'Abierto')->first();
            $statuses = DmihdTicketStatuse::where('status', 'Nuevo')->first();
            $priority = DB::table('dmihd_priority_sub_areas')
                ->select('dmihd_priority_sub_areas.*')
                ->join('dmihd_priorities', 'dmihd_priorities.id', '=',
                    'dmihd_priority_sub_areas.dmihd_priority_id')
                ->where('dmihd_sub_area_id', $request->sub_area)
                ->where('dmihd_priorities.priority', 'Baja')->first();
            $ticket = new DmihdTicket();
            $ticket->subject = $request->affair;
            $ticket->dmihd_sub_area_id = $request->sub_area;
            $ticket->dmihd_status_id = $status->id;
            $ticket->dmihd_ticket_statuses_id = $statuses->id;
            $ticket->dmihd_priority_sub_area_id = $priority->id;
            $ticket->description = $request->description;
            $ticket->user_created_id = $usuario->original[0]->personal_intelisis_id;
            if ($ticket->save()) {
                $participant = new DmihdParticipant();
                $participant->user_id = $request->addressee;
                $participant->hierarchy = 'primary';
                $participant->read = 1;
                $participant->dmihd_ticket_id = $ticket->id;
                $participant->save();
                foreach (json_decode($request->addressee_secundary) as $value) {
                    $participant_secundary = new DmihdParticipant();
                    $participant_secundary->user_id = $value;
                    $participant_secundary->hierarchy = 'secundary';
                    $participant_secundary->read = 1;
                    $participant_secundary->dmihd_ticket_id = $ticket->id;
                    $participant_secundary->save();
                }
                if ($request->contador != 'null') {
                    for ($i = 0; $i <= intval($request->contador); $i++) {
                        $file = $request->file('file' . $i);
                        $nombre = 'tickets/' . $ticket->id . '/' . $file->getClientOriginalName();
                        \Storage::disk('local')->put($nombre, \File::get($file));
                        $archivo = new DmihdFile();
                        $archivo->type_file = $file->getClientMimeType();
                        $archivo->url = $nombre;
                        $archivo->save();
                        $relation = new DmihdFileTicket();
                        $relation->dmihd_ticket_id = $ticket->id;
                        $relation->dmihd_file_id = $archivo->id;
                        $relation->save();
                    }
                }
                return response()->json(['success' => 'Se actualizo exitosamente.'], 200);
            }
            return response()->json(['error' => 'Error al guardar'], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th], 500);
        }
    }

    public function show($id){
        $res=DmihdTicket::with('participants','participants.user',
        'subArea','subArea.area','subArea.area.location')->where('id',$id)->get();
        return response()->json($res, 200);
    }

}
